# Production queue and scheduler

This application uses Laravel's `database` queue connection for integration imports. Each company integration is queued independently, so a slow or failed provider request does not block a web request or another company.

## Required production environment

Set these values in the production `.env` (never commit that file):

```dotenv
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=database
QUEUE_DRIVER=database
```

`QUEUE_CONNECTION` is the Laravel 10 setting. `QUEUE_DRIVER` is retained because this application has an older queue configuration that still reads it.

## First deployment / each release

Run these commands from the deployed application directory after taking a tested staging backup. Do **not** run migrations against production before they have completed successfully on a copied staging database.

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart propfincrm-worker:*
```

Install [`deploy/supervisor/propfincrm-worker.conf`](../deploy/supervisor/propfincrm-worker.conf) into `/etc/supervisor/conf.d/`, changing the PHP executable, application path, Unix user, and log path for the server. It runs two workers for the `integrations` and `default` queues. The worker uses `--max-time=3600` so it is restarted regularly and picks up deployed code.

Install the one-line cron entry from [`deploy/cron/propfincrm-scheduler.cron`](../deploy/cron/propfincrm-scheduler.cron) in the server crontab (or an equivalent system scheduler). Laravel evaluates scheduled jobs every minute. It queues due lead integrations hourly and runs lead auto-assignment company by company each minute.

## Operations

```bash
php artisan schedule:list
php artisan queue:failed
php artisan queue:retry all
php artisan queue:restart
php artisan leads:auto-assign
```

Use `queue:restart` after every deployment if Supervisor is already running. Investigate a failed integration through its `last_error` value and Laravel's `failed_jobs` table; do not place provider secrets in logs.

## Staging verification

1. Restore a production database copy into a separate staging database.
2. Point staging `.env` to that database and use non-production provider credentials. Set `QUEUE_CONNECTION=database`.
3. Record current row counts for leads, clients, tasks, notes, documents, invoices, and master-data tables.
4. Run `php artisan migrate --force`, then confirm the migration creates one initial Company, an active membership for every existing user, and no `company_id` is null in the scoped tables. Existing users should select that company automatically on their next login.
5. Confirm legacy roles were mapped as follows: Administrator/Super Administrator → Company Admin; Team Leader → Team Leader; Employee or unrecognised/no role → Employee. Review this before allowing new company-specific role changes.
6. Compare post-migration row counts with the snapshot. They must match; every existing record belongs to the initial company.
7. Run `php artisan test`. Then enable one active integration for each test company and run `php artisan leads:sync-integrations --company=ID`.
8. Run `php artisan queue:work database --queue=integrations --once`, verify only that company's leads and mapped source were created, then repeat with a second company.
9. Verify a disabled or `manual` integration is not queued, and a bad credential creates a failed job and `last_error` without exposing credentials.
