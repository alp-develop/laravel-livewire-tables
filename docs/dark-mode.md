# Dark Mode

Dark mode is supported across all themes (Tailwind, Bootstrap 5, Bootstrap 4). All dark CSS rules are scoped to the `.lt-dark` class.

To activate dark mode you need two things:

1. **CSS class**: Add `lt-dark` to `<html>` and dispatch the browser event `lt-dark-toggled` — the table reacts instantly via Alpine.
2. **Session**: Store a truthy value in the session key defined by `selector` — the table reads it automatically in `boot()` and sets `$this->darkMode`.

## Configuration

```php
'dark_mode' => [
    'enabled' => true,
    'selector' => 'lt-dark',
    'colors' => [
        'bg' => '#0f172a',
        'bg-card' => '#1e293b',
        'bg-subtle' => '#334155',
        'border' => '#334155',
        'text' => '#f1f5f9',
        'text-muted' => '#94a3b8',
    ],
],
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | `bool` | `false` | Generates dark CSS and enables the `.lt-dark` toggle |
| `selector` | `string` | `lt-dark` | Session key the package reads to detect dark mode |
| `colors` | `array` | Slate | Dark palette (bg, bg-card, bg-subtle, border, text, text-muted) |

When `enabled` is `false`, no dark CSS is generated and `$this->darkMode` is always `false`.

### `selector`

The `selector` is the Laravel session key the package reads on every request. The package only reads — never writes. You control how to store the value.

```php
'selector' => 'lt-dark',     // default
'selector' => 'my_app_dark', // custom key
```

### Color Presets

| Preset | bg | bg-card | bg-subtle | border | text | text-muted |
|--------|----|---------|-----------|--------|------|------------|
| Slate (default) | `#0f172a` | `#1e293b` | `#334155` | `#334155` | `#f1f5f9` | `#94a3b8` |
| Zinc | `#18181b` | `#27272a` | `#3f3f46` | `#3f3f46` | `#fafafa` | `#a1a1aa` |
| Neutral | `#171717` | `#262626` | `#404040` | `#404040` | `#fafafa` | `#a3a3a3` |

## Toggling Dark Mode

Two steps: toggle the CSS class (visual) and write to session (server-side).

### 1. CSS class + browser event

```javascript
document.documentElement.classList.toggle('lt-dark');
window.dispatchEvent(new Event('lt-dark-toggled'));
```

This applies dark styles instantly. Use `localStorage` to persist across page reloads:

```javascript
localStorage.setItem('lt-dark', document.documentElement.classList.contains('lt-dark') ? '1' : '0');
```

Restore in `<head>` (before CSS to prevent flash):

```html
<script>
    if (localStorage.getItem('lt-dark') === '1') {
        document.documentElement.classList.add('lt-dark');
    }
</script>
```

### 2. Write to session

Store a truthy value in the session key matching `selector`. Do it however fits your app:

```php
session(['lt-dark' => true]);
```

The table reads this value automatically on every request. No extra code needed in your tables.

## `$this->darkMode`

Every table component has `public bool $darkMode`. The package sets it in `boot()` before `configure()` runs:

```php
if (config('livewire-tables.dark_mode.enabled', false)) {
    $selector = config('livewire-tables.dark_mode.selector', 'lt-dark');
    $this->darkMode = (bool) session($selector, false);
}
```

Use it in `configure()` to apply conditional classes:

```php
public function configure(): void
{
    $dark = $this->darkMode;
    $this->setHeadClass($dark ? 'lt-thead-tinted' : 'bg-light text-secondary');
    $this->setRowClass(fn ($row) => $dark ? 'custom-dark-row' : '');
}
```

## How It Works

1. Your JS toggles `lt-dark` on `<html>` and dispatches `lt-dark-toggled`
2. Alpine applies `.lt-dark` on the table container instantly
3. Your app writes dark state to session (the key from `selector`)
4. On the next request, `boot()` reads the session and sets `$this->darkMode`
5. `configure()` runs with the correct value

## Cross-Theme Support

Dark mode works identically across all themes:

| Theme | Status |
|-------|--------|
| Tailwind | Supported |
| Bootstrap 5 | Supported |
| Bootstrap 4 | Supported |
