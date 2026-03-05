# Installation

## Requirements

| Dependency | Version                       |
|------------|-------------------------------|
| PHP        | 8.1 – 8.4                    |
| Laravel    | 10.x, 11.x, or 12.x         |
| Livewire   | 3.x or 4.x                   |

## Install via Composer

```bash
composer require alp-develop/laravel-livewire-tables
```

The package auto-discovers its service provider. No manual registration is needed.

## Publish Config (Optional)

```bash
php artisan vendor:publish --tag=livewire-tables-config
```

This publishes `config/livewire-tables.php` with theme, color palette, search debounce, and namespace settings.

## Publish Views (Optional)

```bash
php artisan vendor:publish --tag=livewire-tables-views
```

Views will be copied to `resources/views/vendor/livewire-tables/`.

## Publish Translations (Optional)

```bash
php artisan vendor:publish --tag=livewire-tables-lang
```

Translations will be copied to `lang/vendor/livewire-tables/`.

Included languages: English (`en`), Spanish (`es`), Portuguese (`pt`).

## Generate a Table

```bash
php artisan make:livewiretable UsersTable User
```

This generates `app/Livewire/Tables/UsersTable.php` with a ready-to-edit scaffold.

Supports subdirectories:

```bash
php artisan make:livewiretable Admin/UsersTable User
```

## Use in Blade

```blade
<livewire:tables.users-table />
```

## Bootstrap Theme

To use Bootstrap 5 instead of Tailwind:

```php
// config/livewire-tables.php
'theme' => 'bootstrap',
```

Make sure Bootstrap 5 CSS is loaded in your layout.
