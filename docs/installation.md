# Installation

## Requirements

| Dependency | Version                       |
|------------|-------------------------------|
| PHP        | 8.x+                         |
| Laravel    | 10.x+                        |
| Livewire   | 3.x, 4.x                    |

## Install via Composer

```bash
composer require alp-develop/laravel-livewire-tables
```

The package auto-discovers its service provider. No manual registration is needed.

## Publish Config (Required)

```bash
php artisan vendor:publish --tag=livewire-tables-config
```

This publishes `config/livewire-tables.php` where you configure the theme, color palette, dark mode, search debounce, and component namespace. **This step is required** for the package to work correctly.

## Publish Views (Optional)

```bash
php artisan vendor:publish --tag=livewire-tables-views
```

Views will be copied to `resources/views/vendor/livewire-tables/`.

## Publish Translations (Optional)

```bash
php artisan vendor:publish --tag=livewire-tables-translations
```

Translations will be copied to `lang/vendor/livewire-tables/`.

Included languages: English (`en`), Spanish (`es`), Portuguese (`pt`), French (`fr`), German (`de`), Italian (`it`), Dutch (`nl`), Polish (`pl`), Russian (`ru`), Chinese (`zh`), Japanese (`ja`), Korean (`ko`), Turkish (`tr`), Indonesian (`id`).

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

## Themes

Set the theme in `config/livewire-tables.php`:

```php
'theme' => 'tailwind',
```

| Theme | Value | Alias |
|-------|-------|-------|
| Tailwind CSS | `tailwind` | — |
| Bootstrap 5 | `bootstrap-5` | `bootstrap5`, `bootstrap` |
| Bootstrap 4 | `bootstrap-4` | `bootstrap4` |

Make sure the corresponding CSS framework is loaded in your layout.
