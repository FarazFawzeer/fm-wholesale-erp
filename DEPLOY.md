# Hosting FM Wholesale ERP

The deploy zip already contains `vendor/` (no dev packages) and the built CSS/JS in `public/build/`, so the server does **not** need Node or npm. Composer is optional.

## Server requirements

- PHP 8.2 or newer with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `gd`
- MySQL 5.7+ / MariaDB 10.3+
- Apache with `mod_rewrite` (or Nginx)
- HTTPS (free Let's Encrypt / AutoSSL on most hosts)

## Steps (cPanel / shared hosting)

1. **Create the database.** In cPanel > MySQL Databases, create a database and a user, then give the user ALL PRIVILEGES on it.
2. **Upload the zip** with File Manager and extract it, for example into `/home/<user>/fm-erp`.
3. **Point the domain at `public/`.** In cPanel > Domains, set the document root to `fm-erp/public`.
   If your host does not allow this, extract into the domain folder (for example `public_html`). The included root `.htaccess` sends all traffic into `public/` and blocks `.env`.
4. **Create `.env`.** Copy `.env.production.example` to `.env` and fill in `APP_URL`, `DB_*`, `ADMIN_EMAIL` and `ADMIN_PASSWORD`. Set `SEED_DEMO=true` for a client demo, or `false` for a clean system.
5. **Run these commands** in cPanel > Terminal (or SSH), inside the project folder:
   ```bash
   php artisan key:generate --force
   php artisan migrate --seed --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
6. **Set permissions.** `storage/`, `bootstrap/cache/` and `public/uploads/` must be writable by the web server (755 or 775).
7. Open `https://<your-domain>` and log in with `ADMIN_EMAIL` / `ADMIN_PASSWORD`.

If there is no terminal: create the database on your PC with `php artisan migrate --seed`, export it with phpMyAdmin, and import it on the host. Put an `APP_KEY` generated on your PC (`php artisan key:generate --show`) into the server `.env`.

## After hosting

- With `SEED_DEMO=true`, the demo users `sales@`, `store@` and `accounts@fmwholesale.com` all have the password `password`. Change or deactivate them in **Settings > Users & Roles** before real use.
- There is no public sign-up. Managers create users and reset passwords in Settings > Users & Roles.
- **Updating later:** upload the changed files, then run `php artisan migrate --force` and `php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache`.
- **Backups:** export the database regularly (cPanel > Backup, or `mysqldump`). That is where all the business data lives.
