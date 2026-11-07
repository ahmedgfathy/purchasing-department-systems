<?php

namespace App\Providers;

use App\Support\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Global model auditing: every Eloquent model in the application is
        // covered automatically, including models added in the future.
        foreach (['created', 'updated', 'deleted'] as $modelEvent) {
            Event::listen("eloquent.{$modelEvent}: *", function (string $eventName, array $payload) use ($modelEvent) {
                if (($payload[0] ?? null) instanceof Model) {
                    AuditLogger::model($modelEvent, $payload[0]);
                }
            });
        }

        Event::listen(Login::class, fn (Login $event) => AuditLogger::auth('login', $event->user));
        Event::listen(Logout::class, fn (Logout $event) => AuditLogger::auth('logout', $event->user));
        Event::listen(Failed::class, fn (Failed $event) => AuditLogger::auth(
            'failed_login',
            $event->user,
            $event->credentials['employee_id'] ?? null,
        ));
    }
}
