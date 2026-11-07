<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuditLogger
{
    /**
     * Attributes that must never be written to the audit trail.
     */
    private const SENSITIVE = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    private static ?bool $tableExists = null;

    public static function model(string $event, Model $model): void
    {
        // Never audit the audit table itself (this would recurse forever).
        if ($model instanceof AuditLog) {
            return;
        }

        $attributes = self::clean($model->getAttributes());
        $old = [];
        $new = [];

        if ($event === 'created') {
            $new = $attributes;
        } elseif ($event === 'updated') {
            $changed = self::clean($model->getChanges());
            unset($changed['updated_at']);

            if ($changed === []) {
                return;
            }

            $new = $changed;
            foreach (array_keys($changed) as $key) {
                $old[$key] = $model->getOriginal($key);
            }
        } elseif ($event === 'deleted') {
            $old = $attributes;
        }

        self::record([
            'event' => $event,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'auditable_label' => self::label($model),
            'old_values' => $old !== [] ? $old : null,
            'new_values' => $new !== [] ? $new : null,
        ]);
    }

    public static function auth(string $event, ?Authenticatable $user = null, ?string $identifier = null): void
    {
        self::record([
            'event' => $event,
            'auditable_type' => $user ? $user::class : null,
            'auditable_id' => $user?->getAuthIdentifier(),
            'auditable_label' => $user?->name ?? $identifier ?? $user?->employee_id,
            'user_id' => $user?->getAuthIdentifier(),
            'user_name' => $user?->name ?? $identifier,
            'user_employee_id' => $user?->employee_id ?? $identifier,
            'user_email' => $user?->email,
        ]);
    }

    /**
     * Record a custom audit entry (for example a role/permission pivot change).
     */
    public static function event(string $event, Model $model, array $old = [], array $new = [], ?string $label = null): void
    {
        self::record([
            'event' => $event,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'auditable_label' => $label ?? self::label($model),
            'old_values' => $old !== [] ? $old : null,
            'new_values' => $new !== [] ? $new : null,
        ]);
    }

    public static function record(array $data): void
    {
        if (! self::auditTableExists()) {
            return;
        }

        try {
            $request = app()->runningInConsole() ? null : request();
            $actor = Auth::user();

            AuditLog::create(array_merge([
                'user_id' => $actor?->getAuthIdentifier(),
                'user_name' => $actor?->name,
                'user_employee_id' => $actor?->employee_id,
                'user_email' => $actor?->email,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'url' => $request?->fullUrl(),
                'http_method' => $request?->method(),
            ], $data));
        } catch (Throwable $exception) {
            // Auditing must never break the actual request.
            Log::warning('Unable to write audit log: '.$exception->getMessage());
        }
    }

    private static function auditTableExists(): bool
    {
        if (self::$tableExists === null) {
            try {
                self::$tableExists = Schema::hasTable('audit_logs');
            } catch (Throwable) {
                self::$tableExists = false;
            }
        }

        return self::$tableExists;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private static function clean(array $attributes): array
    {
        foreach (self::SENSITIVE as $key) {
            unset($attributes[$key]);
        }

        return $attributes;
    }

    private static function label(Model $model): string
    {
        foreach (['name', 'title', 'label', 'email', 'slug'] as $attribute) {
            $value = $model->getAttribute($attribute);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return class_basename($model).' #'.$model->getKey();
    }
}
