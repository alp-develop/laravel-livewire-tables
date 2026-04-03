# Security Considerations

This document covers security best practices when using Laravel Livewire Tables. The package includes built-in protections, but some areas require developer attention.

## Built-in Protections

The package provides the following security measures out of the box:

| Protection | Description |
|------------|-------------|
| **Column whitelist** | Only columns explicitly defined in `columns()` are accepted for sorting and searching. Arbitrary field names are rejected. |
| **Field name sanitization** | Sort and search field names are sanitized with `[a-zA-Z0-9_.]` regex before being passed to the query builder. |
| **Sort direction whitelist** | Sort direction is restricted to `asc` or `desc`. Any other value defaults to `asc`. |
| **Filter key validation** | Only filters explicitly defined in `filters()` are accepted. Unknown filter keys are silently discarded. |
| **Parameterized queries** | All filter values (text, date, number, select) use Eloquent parameter binding. Values are never concatenated into SQL. |
| **Per-page whitelist** | The per-page value is validated against `$perPageOptions`. Users cannot request arbitrary page sizes. |
| **CSV formula injection** | Exported CSV values starting with `=`, `+`, `-`, `@`, tab, or carriage return are prefixed with a single quote to prevent formula execution in Excel/Google Sheets. |
| **Image URL validation** | The default image column view only renders `http://`, `https://`, or root-relative (`/`) URLs. Dangerous schemes like `javascript:` and `data:` are blocked. |
| **Action button escaping** | Action button `wire:click` and `class` attributes are escaped with `htmlspecialchars()` to prevent attribute injection. |
| **Bulk checkbox ID encoding** | Row IDs in bulk action checkboxes use `Js::from()` for safe JavaScript encoding. |
| **Session state validation** | Sort fields, filters, and hidden columns are validated against defined columns when loading from session cache. |

## Developer Responsibilities

### Custom Search Callbacks

When using a custom search callback, always use parameter binding:

```php
// SAFE — parameterized
TextColumn::make('name')
    ->searchable(fn (Builder $query, string $search) =>
        $query->orWhere('name', 'LIKE', "%{$search}%")
    ),

// SAFE — parameterized with whereRaw
TextColumn::make('status')
    ->searchable(fn (Builder $query, string $search) =>
        $query->orWhereRaw(
            "CASE WHEN active = 1 THEN 'active' ELSE 'inactive' END LIKE ?",
            ["%{$search}%"]
        )
    ),

// DANGEROUS — SQL injection via string concatenation
TextColumn::make('name')
    ->searchable(fn (Builder $query, string $search) =>
        $query->whereRaw("name LIKE '%{$search}%'")  // DO NOT DO THIS
    ),
```

### Custom Filter Callbacks

The same applies to custom filter callbacks — always use Eloquent methods or parameterized raw queries:

```php
// SAFE
TextFilter::make('name')
    ->filter(fn (Builder $query, mixed $value) =>
        $query->where('name', 'LIKE', "%{$value}%")
    ),

// DANGEROUS — never concatenate filter values into raw SQL
TextFilter::make('name')
    ->filter(fn (Builder $query, mixed $value) =>
        $query->whereRaw("name = '{$value}'")  // DO NOT DO THIS
    ),
```

### BladeColumn and Custom Views

`BladeColumn` uses `{!! !!}` (raw, unescaped output) by design. If your custom Blade view displays user-controlled data, you must escape it:

```php
// In your custom Blade view
<span>{{ $row->name }}</span>          {{-- SAFE — escaped --}}
<span>{!! $row->bio !!}</span>         {{-- DANGEROUS if bio contains user input --}}
```

### Toolbar Slots

Toolbar slots (`toolbarLeftPrepend()`, `beforeTable()`, etc.) render raw HTML. Return only trusted content:

```php
public function toolbarLeftPrepend(): string
{
    // SAFE — hardcoded HTML
    return '<span class="badge">Custom</span>';
}

public function toolbarLeftPrepend(): string
{
    // DANGEROUS — unescaped user input
    return '<span>' . $this->userInput . '</span>';  // DO NOT DO THIS
}
```

### Image URLs

The default image column view blocks `javascript:` and `data:` URIs. If you use a custom view for image columns, apply the same validation:

```php
// The default view only allows: http://, https://, /path
// If you override with ->view('my-custom-image'), validate URLs yourself
ImageColumn::make('avatar'),  // Safe with default view
```

### Action Button Closures

Action button closures should return simple Livewire method calls. The return value is escaped, but keep actions straightforward:

```php
ActionColumn::make()
    ->button('Edit', fn ($row) => "edit({$row->id})")      // Safe — integer ID
    ->button('View', fn ($row) => "view('{$row->slug}')"),  // Safe — escaped by package
```

## Reporting Security Issues

If you discover a security vulnerability, please report it responsibly by emailing the maintainers directly instead of opening a public issue.
