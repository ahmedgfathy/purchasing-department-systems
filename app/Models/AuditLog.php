<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'event',
        'auditable_type',
        'auditable_id',
        'auditable_label',
        'user_id',
        'user_name',
        'user_employee_id',
        'user_email',
        'ip_address',
        'user_agent',
        'url',
        'http_method',
        'old_values',
        'new_values',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjectType(): string
    {
        $type = $this->auditable_type
            ? class_basename($this->auditable_type)
            : 'Auth';

        $key = 'app.models.'.$type;
        $value = __($key);

        return is_string($value) && $value !== $key ? $value : $type;
    }

    public function eventLabel(): string
    {
        $key = 'app.audit_events.'.$this->event;
        $value = __($key);

        if (is_string($value) && $value !== $key) {
            return $value;
        }

        return ucfirst(str_replace('_', ' ', $this->event));
    }

    public function eventColor(): string
    {
        return match ($this->event) {
            'created' => 'green',
            'updated', 'roles_synced', 'permissions_synced' => 'amber',
            'deleted', 'failed_login' => 'red',
            'login' => 'blue',
            'logout' => 'slate',
            default => 'slate',
        };
    }
}
