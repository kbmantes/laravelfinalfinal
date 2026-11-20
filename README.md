# Laravel Final Final — Simple POS

This is a simple Laravel POS project. It includes products, customers, staff sign-in, sales, stock checks, image uploads, and sales history. The original CodeIgniter folder is not changed.

## Requirements

- PHP 8.2 or newer with `fileinfo`, `gd`, `mbstring`, `openssl`, and `pdo_sqlite` enabled.
- Composer 2.

Laravel 12 supports PHP 8.2, including the PHP 8.2.12 version shown in your terminal.

## Check your PHP setup on Windows

In PowerShell, run these commands to see which PHP and Composer versions are active and whether Laravel’s required PHP extensions are enabled:

```powershell
php -v
composer --version
where.exe php
php -m | findstr /I "fileinfo gd mbstring openssl pdo_sqlite sqlite3"
```

This project requires PHP 8.2 or newer. The extension check should list `fileinfo`, `gd`, `mbstring`, `openssl`, and `pdo_sqlite`. If you need help checking compatibility, send me the output of those commands.

## Run on Windows

1. Extract this ZIP and open the **final laravel** folder in VS Code. It is the folder containing `artisan` and `composer.json`.
2. Open **Terminal → New Terminal** and run:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
   ```

3. Open <http://127.0.0.1:8000>.
4. Sign in with `admin` / `Admin123!`.

Keep the terminal running while you use the app. Press Ctrl+C to stop the server.

## CRUD and authentication

- Product CRUD with search, active/archive filters, safe archive/restore, stock and image management.
- Customer CRUD; a customer with sales history cannot be deleted.
- Staff CRUD; passwords are hashed, editing with blank password preserves the old password, and accounts with sales plus the current account cannot be deleted.
- Session-based login/logout, login throttling, authenticated management routes, and CSRF protection.
- Record sale, check stock on the server, atomically decrement inventory, save sale snapshots, and protect repeat submissions.
- Uploaded images are validated, re-encoded to JPEG, stored outside the public folder, and served only to signed-in staff.

## Email configuration

The project includes Laravel mail configuration. It uses the `log` mailer by default, so email messages are recorded in `storage/logs/laravel.log` while working on a school computer. To send real email, copy the SMTP values from your email provider into `.env`, then clear cached settings with `php artisan config:clear`.

To test the setup, run `php artisan mail:test your-email@example.com`. With the default `log` driver, check `storage/logs/laravel.log`; with SMTP settings, the message is sent to the address you provide.

Do not place your normal Gmail password in `.env`; Gmail requires an App Password.

## Database and uploaded images

The included `database/database.sqlite` starts clean. Laravel creates the tables and the seeder creates one `admin` account, three simple products, and two customers. Add your actual staff accounts from the Staff page before demonstrating the system.

If you need to reset the local database, run `php artisan migrate:fresh --seed`. This removes all products, customers, staff, and sales, then creates only the `admin` account.

For a MySQL setup, update the `DB_*` values in `.env`, create the database first, then run `php artisan migrate --seed`.

## Optional phpMyAdmin setup

phpMyAdmin is not required because the default database is SQLite. If your class requires phpMyAdmin, start MySQL in XAMPP, open phpMyAdmin, create a database named `laravel_pos`, and change `.env` to:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_pos
DB_USERNAME=root
DB_PASSWORD=
```

Then run `php artisan migrate --seed`. Use the SQLite setup if MySQL is not available.

## Notes

- `.env` is intentionally not included. Create it from `.env.example`; never publish `.env` or real credentials.
- `vendor/` is intentionally not included. `composer install` creates it.
- Existing staff password hashes are preserved in the SQLite database. If an existing account password is unknown, use an account you control or reset it in the database before use.
