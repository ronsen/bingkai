# Bingkai

## Tech stack

- **Laravel 13** with PHP 8.3+
- **SQLite** by default; any Laravel-supported database can be configured
- **Tailwind CSS 4** and **Vite 8** for the frontend
- **Intervention Image 4** for preview generation
- **Pest 5** for testing and **Laravel Pint** for code formatting

## Requirements

- PHP 8.3 or newer with the `fileinfo`, `exif`, `pdo_sqlite`, and either `gd` or `imagick` extensions
- Composer 2
- Node.js `^20.19.0` or `>=22.12.0`
- npm
- A mail transport for magic links, or the `log` mail driver for local development

## Installation

### 1. Clone and install dependencies

```bash
git clone <repository-url> bingkai
cd bingkai

composer install
```

### 2. Create the environment file

```bash
cp .env.example .env
php artisan key:generate
```

Set the application name and URL in `.env`:

```dotenv
APP_NAME=Bingkai
APP_URL=http://localhost:8000
```

### 3. Prepare the database

SQLite is the default connection. Create the database file if it does not exist:

```bash
touch database/database.sqlite
```

Then run the migrations and seeders:

```bash
php artisan migrate
```

To use MySQL or PostgreSQL instead, update the `DB_*` values in `.env` and run the same migration command.

### 4. Configure mail

Magic links are sent by email. For local development, the default `MAIL_MAILER=log` writes the email to `storage/logs/laravel.log`:

```dotenv
MAIL_MAILER=log
```

Watch the log and open the sign-in link from the latest email:

```bash
tail -f storage/logs/laravel.log
```

For production, configure a real mail transport (SMTP, SES, Postmark, etc.) in `.env`. Make sure `APP_URL` matches the public URL of the application, because signed magic links are generated from it.

### 5. Link public storage

Preview images are served from the public storage disk:

```bash
php artisan storage:link
```

### 6. Build frontend assets

For development:

```bash
npm install
npm run dev
```

For a production build:

```bash
npm install
npm run build
```

### 7. Start the application

```bash
php artisan serve
```

The application is available at `http://localhost:8000`.

### Quick setup shortcut

The repository also provides a Composer setup script. It installs PHP dependencies, creates `.env`, generates the application key, runs migrations, installs npm dependencies, and builds assets:

```bash
touch database/database.sqlite
composer run setup
php artisan migrate --seed
php artisan storage:link
```

The script does not seed users or create the storage symlink, so run those two commands afterward.

## Seeded accounts

Password login is disabled. Sign in by requesting a magic link for that address. With `MAIL_MAILER=log`, the link appears in `storage/logs/laravel.log`.

To add another account, run the interactive command:

```bash
php artisan user:add
```

It prompts for a name and email address, then creates a passwordless account.

## Image storage

Generated previews are stored on the disk configured by `IMAGE_STORAGE_DISK` (default: `public`).

New uploads are stored directly in the `images` directory. A random filename (`{random-string}.webp`) is generated and recorded in the `images.filename` column:

```text
storage/app/public/images/{random-string}.webp
```

No per-image subdirectory is created. Only the optimized preview is kept; the uploaded source file is discarded after processing.

## Configuration

Key image-related environment variables:

| Variable | Default | Description |
| --- | --- | --- |
| `IMAGE_STORAGE_DISK` | `public` | Filesystem disk used for generated previews |
| `IMAGE_MAX_DIMENSION` | `1920` | Maximum width or height of generated previews |
| `IMAGE_QUALITY` | `85` | WebP preview quality |
| `IMAGE_MAX_UPLOAD_SIZE` | `25600` | Maximum upload size in kilobytes (25 MB) |
| `IMAGE_SHOW_GPS_DEFAULT` | `false` | Whether GPS coordinates are shown by default |
| `IMAGE_CACHE_TTL` | `3600` | Cache lifetime for gallery and image data, in seconds |

The full configuration lives in `config/images.php`.

## Development

Run the combined development processes:

```bash
composer run dev
```

Or run the backend and frontend separately:

```bash
php artisan serve
npm run dev
```

## Testing and formatting

Run the test suite:

```bash
php artisan test
```

Format modified PHP files:

```bash
vendor/bin/pint --dirty --format agent
```

The Composer `test` script clears configuration and runs the suite:

```bash
composer run test
```

## Security notes

- Magic links are temporary signed URLs, expire after 15 minutes, and are single-use.
- Image management routes require an authenticated session.
- Uploaded files are validated by content type, size, and image format.
- Uploaded source files are discarded after the optimized preview is generated.
- GPS coordinates are hidden by default.

## License

This application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
