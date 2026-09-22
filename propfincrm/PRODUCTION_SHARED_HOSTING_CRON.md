# Production Guide: Shared Hosting Cron Setup & Lead Integrations (99acres & Others)

This guide provides the exact configuration, cron commands, and troubleshooting procedures for running automated lead integrations (including **99acres XML API**) on **shared hosting environments** (such as cPanel, Plesk, DirectAdmin, Hostinger, GoDaddy, Namecheap, etc.).

---

## 1. Why Shared Hosting Requires Special Cron Handling

On standard VPS or dedicated servers, background tasks are typically handled by long-running daemon supervisors like `supervisord` running `php artisan queue:work`.

However, on **shared hosting**:
1. **No background daemons / supervisor**: Process managers like `supervisord` are usually not permitted or installed.
2. **Process time limits**: Long-running PHP CLI processes are killed after 60–120 seconds.
3. **Queue bottleneck**: If jobs are pushed to the database queue without an active queue runner, leads will sit unprocessed in the `jobs` database table.

### Solution:
ElyLeads / PropFin CRM includes a dedicated synchronous execution flag (`--now`):
```bash
php artisan leads:sync-integrations --now
```
With `--now`, the cron command immediately connects directly to 99acres and other configured lead providers, fetches new leads, normalizes phone numbers, creates CRM leads, tags lead sources, and updates sync metrics without depending on background queue daemons.

---

## 2. Choosing Your Cron Setup

Select either **Option A** (Recommended universal method) or **Option B** (Dedicated direct cron).

### Option A: Universal Laravel Scheduler (Recommended)
This runs Laravel's master scheduler every minute. Laravel will automatically trigger `leads:sync-integrations --now` every 15 minutes, and respect individual integration frequencies (`every_15_minutes`, `hourly`, `daily`, etc.).

#### Cron Entry:
```bash
* * * * * cd /home/USERNAME/public_html && php artisan schedule:run >> /dev/null 2>&1
```
*(Replace `/home/USERNAME/public_html` with your actual server document root. On some shared hosts, specify the full PHP binary path, e.g., `/usr/local/bin/php` or `/usr/bin/php8.2`)*.

---

### Option B: Dedicated Lead Sync Cron (Direct Execution with Logging)
If your shared host limits cron jobs or you want a dedicated log file to monitor lead fetching:

#### 1. Every 15 Minutes (Recommended for Live Real Estate Feeds):
```bash
*/15 * * * * cd /home/USERNAME/public_html && php artisan leads:sync-integrations --now >> /home/USERNAME/public_html/storage/logs/lead-sync.log 2>&1
```

#### 2. Every 30 Minutes:
```bash
*/30 * * * * cd /home/USERNAME/public_html && php artisan leads:sync-integrations --now >> /home/USERNAME/public_html/storage/logs/lead-sync.log 2>&1
```

#### 3. Every Hour (At the start of every hour):
```bash
0 * * * * cd /home/USERNAME/public_html && php artisan leads:sync-integrations --now >> /home/USERNAME/public_html/storage/logs/lead-sync.log 2>&1
```

#### 4. Twice Daily (Every 12 Hours at 08:00 and 20:00):
```bash
0 8,20 * * * cd /home/USERNAME/public_html && php artisan leads:sync-integrations --now >> /home/USERNAME/public_html/storage/logs/lead-sync.log 2>&1
```

#### 5. Once Daily (Every Night at 02:00 AM):
```bash
0 2 * * * cd /home/USERNAME/public_html && php artisan leads:sync-integrations --now >> /home/USERNAME/public_html/storage/logs/lead-sync.log 2>&1
```

#### 6. Once Weekly (Every Sunday at 03:00 AM):
```bash
0 3 * * 0 cd /home/USERNAME/public_html && php artisan leads:sync-integrations --now >> /home/USERNAME/public_html/storage/logs/lead-sync.log 2>&1
```

#### 7. Specific Single Integration (Force Sync regardless of schedule):
```bash
*/15 * * * * cd /home/USERNAME/public_html && php artisan leads:sync-integrations --id=1 --force --now >> /home/USERNAME/public_html/storage/logs/99acres-sync.log 2>&1
```

---

### Option C: Queue Worker via Cron (If using queue driver)
If your application `QUEUE_CONNECTION=database` and you prefer background asynchronous dispatching:
```bash
* * * * * cd /home/USERNAME/public_html && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```
This runs every minute, processes all pending jobs until empty or 50 seconds elapse, then cleanly exits before the next minute's cron fires.

---

## 3. How to Configure Cron in cPanel / Plesk

### In cPanel:
1. Log in to your **cPanel** dashboard.
2. Scroll to the **Advanced** section and click on **Cron Jobs**.
3. Under **Add New Cron Job**:
   - **Common Settings**: Choose `Once Per Minute (* * * * *)` (for Option A) or `Every 15 minutes (*/15 * * * *)` (for Option B).
   - **Command**:
     ```bash
     cd /home/YOUR_CPANEL_USER/public_html && php artisan schedule:run >> /dev/null 2>&1
     ```
     *(Or with direct path: `/usr/local/bin/ea-php82 /home/YOUR_CPANEL_USER/public_html/artisan schedule:run >> /dev/null 2>&1`)*
4. Click **Add New Cron Job**.

### In Plesk:
1. Go to **Websites & Domains** > **Scheduled Tasks (Cron Jobs)**.
2. Click **Add Task**.
3. Task Type: **Run a PHP script**.
4. Script path: `artisan` (with arguments `schedule:run` or `leads:sync-integrations --now`).
5. Select the PHP version matching your website (PHP 8.2 or 8.3).
6. Set cron interval and click **OK**.

---

## 4. Supported Sync Frequencies in Company Settings

Under **Settings > Lead Integrations**, you can set any of the following sync frequencies:

| Frequency Value in UI | Description | Best Used For |
|---|---|---|
| **Every 15 minutes** | Polls provider every 15 mins with a 15-min overlap buffer | Hot real estate leads where fast response time is vital |
| **Every 30 minutes** | Polls provider every 30 mins | Moderate enquiry volume |
| **Hourly** | Polls at the beginning of each hour | Regular feeds |
| **Twice daily** | Polls every 12 hours | Low-frequency portals or bulk imports |
| **Daily** | Polls once every 24 hours | Daily summary feeds |
| **Weekly** | Polls once every 7 days | Periodic bulk partner updates |
| **Manual / Custom Cron** | Never polled automatically by general scheduler | Only synced when clicking **Test Sync Now** in UI or via dedicated cron with `--id=X --force` |

---

## 5. 99acres XML API Setup & Checklist

### 1. Mandatory Outbound IP Whitelisting
> [!IMPORTANT]
> **99acres strictly enforces IP whitelisting.**
> If your shared hosting server's outbound IP is not whitelisted by 99acres, any API call will be rejected with an XML error response.

To find your server's outbound public IP:
- Connect to SSH or cPanel Terminal and run:
  ```bash
  curl -s ifconfig.me
  ```
- Send this IP address to your 99acres account manager or relationship manager to whitelist it for your XML Lead Query account.

### 2. Required 99acres Integration Credentials in CRM:
- **Provider**: `99acres`
- **Sync Frequency**: `every_15_minutes` (or your preferred frequency)
- **API Endpoint URL**:
  `https://www.99acres.com/99api/v1/getmy99/xml/fetchxmlqueries/query.xml`
  *(or the exact custom query endpoint provided by your 99acres account manager)*
- **Username / API Key**: Your 99acres XML account username.
- **Password / API Secret**: Your 99acres XML account password.
- **Assign Lead Source**: Select an existing Lead Source (e.g. `99acres`) or leave as default.

### 3. Testing 99acres Connection:
You can verify the connection immediately in two ways:
1. **Via UI**: In **Settings > Lead Integrations**, locate your configured 99acres card and click **Test Sync Now**.
   - If successful: You will see a green confirmation badge showing the number of leads received, new leads created, and duplicate leads skipped.
   - If failed: You will see an immediate red banner displaying the exact error returned by 99acres (e.g., `IP Not Whitelisted` or `Invalid Username/Password`).
2. **Via Command Line**:
   ```bash
   php artisan leads:sync-integrations --force --now
   ```

---

## 6. Verification & Troubleshooting

### Check Real-Time Logs:
If you set up dedicated logging in Option B, you can inspect the sync log at any time:
```bash
tail -f storage/logs/lead-sync.log
```
Or view the standard Laravel log:
```bash
tail -f storage/logs/laravel.log
```

### Common Issues & Resolutions:
1. **Error: "99acres API error: IP X.X.X.X is not whitelisted"**:
   - Contact 99acres support with the IP shown in the error message to add it to their whitelist.
2. **Error: "99acres API error: Invalid User Name or Password"**:
   - Click **Edit** on the integration in Company Settings, enter the correct username and password, and save.
3. **Leads not appearing automatically**:
   - Check if your cron job is active in cPanel/Plesk.
   - Run `php artisan schedule:list` in terminal to confirm `leads:sync-integrations` is scheduled.
   - Ensure the integration toggle is switched to **Enable this integration**.
4. **Duplicate prevention**:
   - The sync service automatically prevents duplicates based on normalized 10-digit mobile number, company ID, and existing lead history within the time window.

---

## 7. Useful Artisan Commands Reference

| Command | Purpose |
|---|---|
| `php artisan leads:sync-integrations --now` | Run sync immediately for all due integrations |
| `php artisan leads:sync-integrations --force --now` | Force sync immediately for all active integrations (ignoring time schedules) |
| `php artisan leads:sync-integrations --id=1 --force --now` | Force sync immediately for integration ID #1 |
| `php artisan schedule:list` | View all scheduled tasks and next run times |
| `php artisan schedule:run` | Manually simulate the 1-minute master cron scheduler |
| `php artisan optimize:clear` | Clear all cache (config, routes, views) after server updates |
