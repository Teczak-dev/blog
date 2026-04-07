# Blog

A blog application built with Laravel 12, Filament v5, and Tailwind CSS v4. Features post management with a Filament admin panel.

## Tech Stack

- **PHP** 8.4
- **Laravel** 12
- **Filament** 5
- **Tailwind CSS** 4
- **SQLite** (default)
- **Pest** 4 (testing)

## Installation

```bash
git clone https://github.com/Teczak-dev/blog-2-school.git
cd blog-2-school
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan boost:install
npm run build
```

## Running the Application

```bash
composer run dev
```

This starts the Laravel dev server, queue worker, log viewer, and Vite simultaneously.

## Testing

```bash
php artisan test
```

## Code Formatting

```bash
vendor/bin/pint
```
