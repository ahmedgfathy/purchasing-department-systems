# Purchasing System

Laravel 12 application with role-based access control (RBAC) and a global audit trail. The front end is rendered entirely with Blade templates, served by Nginx at **http://ppd.local**, backed by PostgreSQL.

## Stack

| Component | Value |
| --- | --- |
| Framework | Laravel 12 |
| PHP | 8.4 (PHP-FPM) |
| Database | PostgreSQL 17, database `ppd` |
| Web server | Nginx (`/etc/nginx/sites-available/ppd.local`) |
| Front end | Blade templates only (no JS framework) |

## Sign in

| Field | Value |
| --- | --- |
| URL | http://ppd.local |
| Email | `admin@ppd.local` |
| Password | `Admin@12345` |

Change the password after the first login. The first page is the login screen; guests are always redirected there.

## Database

The connection lives in `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ppd
DB_USERNAME=xinreal
DB_PASSWORD=ppd_local_dev_2026
```

## RBAC

Roles are made of permissions; users can hold multiple roles.

- **Users** — CRUD, role assignment, login tracking (last login time + IP).
- **Roles & permissions** — CRUD, permission checkboxes grouped by module.
- **Seeded roles**
  - `super-admin` — bypasses every permission check. System role.
  - `admin` — all permissions explicitly granted. System role.
  - `user-manager` — manage users.
  - `auditor` — view the audit trail.
  - `viewer` — dashboard only.
- **Permissions** — `dashboard.view`, `users.view/create/update/delete`, `roles.view/create/update/delete`, `audit.view`.

Authorization is enforced by the `permission` middleware (`app/Http/Middleware/EnsureUserHasPermission.php`) on every route and by `hasPermission()` checks in navigation and views.

## Audit trail

Auditing is global: every Eloquent model is covered automatically through wildcard model events registered in `app/Providers/AppServiceProvider.php`, so new models are audited without extra code.

Recorded events:

- `created`, `updated`, `deleted` — every model change.
- `login`, `logout`, `failed_login` — authentication events.
- `roles_synced`, `permissions_synced` — RBAC pivot changes.

Each entry stores **who** (user id, name, email snapshot), **which** (model class, id, label), **where** (URL, HTTP method, IP, user agent) and **when** (timestamp), plus old and new values as JSON. Passwords and remember tokens are never stored. Audit records are read-only in the UI.

The audit screens are available under **Audit trail** for users with the `audit.view` permission, with filters for event, record type, user/record/IP/URL search and date range.

## Useful commands

```bash
php artisan migrate --seed          # apply migrations and seed RBAC data
php artisan optimize:clear          # clear config/route/view caches
php artisan route:list              # list routes
sudo bash deploy/setup-vhost.sh     # reinstall/refresh the Nginx vhost
```

Vhost source: `deploy/ppd.local.conf`.
