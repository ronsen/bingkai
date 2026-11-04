# Bingkai Photography

Bingkai is a Laravel-powered photography gallery and timeline. It preserves original high-resolution photographs and EXIF metadata while serving optimized previews to visitors. Sign-in is passwordless, image management is restricted to administrators, and full-resolution downloads require an authenticated account.

## Features

### Public gallery and timeline

- Chronological photography timeline with pagination.
- Responsive gallery and image detail pages with light and dark themes.
- Automatic EXIF extraction and display: camera make/model, lens, exposure time, aperture, ISO, focal length, dimensions, and capture date.
- GPS coordinates are hidden by default and can be revealed per image.
- Optimized WebP previews are generated automatically while the original file remains untouched.

### Accounts and roles

| Role | Access |
| --- | --- |
| **Admin** | Upload, edit, and delete photographs. Browse, download originals, and manage their profile. |
| **User** | Browse the gallery, download originals, and manage their profile. |
| **Guest** | Browse the public gallery. Must sign in to download originals. |

Authentication uses email magic links:

- Sign-in is passwordless: enter an email address and receive a signed, single-use link.
- Links expire after **15 minutes** and become invalid after first use.
- New accounts can be created from the registration page.
- Accounts are created without a password. A password can optionally be set later from the profile page.
- Successful magic-link sign-in marks the email address as verified.

### Image management

- Only administrators can upload, edit, or delete photographs.
- Uploads accept JPEG, PNG, and WebP files up to 25 MB by default.
- The original full-resolution file is stored unchanged.
- A resized WebP preview (1920 px maximum dimension by default) is generated for the public gallery.
- Original files are stored with unguessable UUID filenames.
- Full-resolution downloads are served only to authenticated users.
- Timeline and individual image responses are cached to reduce database load.

### User profile

- Update display name and email address.
- Optionally set or update an account password.
- Existing passwords require the current password before changing.

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
php artisan migrate --seed
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

`php artisan migrate --seed` creates two local accounts:

| Email | Role |
| --- | --- |
| `test@example.com` | User |
| `admin@example.com` | Admin |

Password login is disabled. Sign in by requesting a magic link for either address. With `MAIL_MAILER=log`, the link appears in `storage/logs/laravel.log`.

## Managing roles

New registrations receive the `user` role. Administrators can be created through the seeder or promoted from the command line:

```bash
php artisan tinker --execute 'App\Models\User::where("email", "you@example.com")->first()->update(["role" => App\Enums\Role::Admin]);'
```

The available roles are defined in `app/Enums/Role.php`:

- `admin`
- `user`
- `guest`

## Image storage and maintenance

Originals and generated previews are stored on the disk configured by `IMAGE_STORAGE_DISK` (default: `public`).

New uploads are stored as:

```text
storage/app/public/images/{image-uuid}/{random-uuid}.{extension}   # original
storage/app/public/images/{image-uuid}/standard.webp               # optimized preview
```

## Configuration

Key image-related environment variables:

| Variable | Default | Description |
| --- | --- | --- |
| `IMAGE_STORAGE_DISK` | `public` | Filesystem disk used for originals and previews |
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
- Image management routes require the `admin` role.
- Full-resolution downloads require an authenticated session.
- Original filenames are randomized so visitors cannot guess the high-resolution file URL.
- Uploaded files are validated by content type, size, and image format.
- GPS coordinates are hidden by default.
- Public previews never fall back to exposing the original file path.

## License

This application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
