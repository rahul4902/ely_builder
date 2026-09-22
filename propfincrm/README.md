# ElyLeads

## Security configuration

- Firebase service-account JSON is read from `FIREBASE_CREDENTIALS_PATH`, defaulting to `storage/app/firebase/service-account.json`. Never place it under `public/` or commit it to source control.
- Set `FIREBASE_PROJECT_ID` when the service-account JSON does not contain the project ID.
- The external auto-assignment scheduler is a `POST` endpoint and requires the `X-Scheduler-Token` header (or a Bearer token) to match `SCHEDULER_TOKEN`.
- Production must use `APP_ENV=production` and `APP_DEBUG=false`.

## Tests

Run the security route checks with:

```bash
php artisan test
```
