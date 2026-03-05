# Columns

Columns define which fields from your model are displayed in the table. All columns extend the base `Column` class and implement `ColumnContract`.

## Column Types

| Type | Class | Description |
|------|-------|-------------|
| Text | `TextColumn` | Plain text display |
| Boolean | `BooleanColumn` | True/false with visual indicator |
| Date | `DateColumn` | Date values with configurable format |
| Image | `ImageColumn` | Image with lightbox preview |
| Blade | `BladeColumn` | Fully custom rendering via closure + Blade |
| Action | `ActionColumn` | Row action buttons (edit, delete, custom) |

## Creating Columns

### TextColumn

```php
use Livewire\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->label('Full Name')
    ->sortable()
    ->searchable()
    ->format(fn(mixed $value) => strtoupper($value));
```

#### Computed TextColumn (no database field)

Create a TextColumn without a database field by calling `make()` with no arguments. Use `render(Closure)` to define the cell content:

```php
TextColumn::make()
    ->render(fn($row) => $row->first_name . ' ' . $row->last_name)
    ->label('Full Name');
```

### BooleanColumn

```php
use Livewire\Tables\Columns\BooleanColumn;

BooleanColumn::make('active')
    ->label('Status')
    ->sortable();
```

Renders a visual true/false badge automatically.

#### Custom Labels

```php
BooleanColumn::make('active')
    ->labels('Active', 'Inactive');
```

#### Custom Rendering

BooleanColumn respects `render()` and `format()` callbacks. If set, they take priority over the default badge rendering:

```php
BooleanColumn::make('active')
    ->render(fn($row) => $row->active ? '✅' : '❌');

BooleanColumn::make('active')
    ->format(fn($value, $row) => $value ? 'Enabled' : 'Disabled');
```

Priority order: `render()` > `format()` > default labels.

### DateColumn

```php
use Livewire\Tables\Columns\DateColumn;

DateColumn::make('created_at')
    ->label('Created')
    ->sortable()
    ->format('d/m/Y');
```

The `format()` method accepts:
- **String** — PHP date format (e.g., `'Y-m-d'`, `'M d, Y'`, `'d/m/Y H:i'`)
- **Closure** — Custom formatting callback

```php
DateColumn::make('created_at')
    ->format(fn(mixed $value, $row) => $value?->diffForHumans());
```

Handles `DateTimeInterface`, strings, and `null` values automatically.

### ImageColumn

```php
use Livewire\Tables\Columns\ImageColumn;

ImageColumn::make('avatar_url')
    ->label('Photo')
    ->alt('name')
    ->dimensions(48, 48)
    ->width('64px');
```

| Method | Description |
|--------|-------------|
| `alt(string $field)` | Model field used for the `alt` attribute |
| `dimensions(int $w, int $h)` | Image display dimensions |
| `width(string $css)` | Column CSS width |

Click to open a lightbox preview (Alpine.js `x-teleport`).

### BladeColumn

The most flexible column type. Render any Blade view or HTML via a closure:

```php
use Livewire\Tables\Columns\BladeColumn;

BladeColumn::make()
    ->label('Status')
    ->render(fn($row, $table) => view('tables.user-status', [
        'user'  => $row,
        'table' => $table,
    ]));
```

The closure receives:
- `$row` — The Eloquent model for the current row
- `$table` — The Livewire `DataTableComponent` instance

To make a BladeColumn searchable, pass a closure with the query builder and search term:

```php
BladeColumn::make()
    ->label('Full Name')
    ->searchable(fn(Builder $query, string $search) => $query
        ->orWhere('first_name', 'LIKE', "%{$search}%")
        ->orWhere('last_name', 'LIKE', "%{$search}%")
    )
    ->render(fn($row) => $row->first_name . ' ' . $row->last_name);
```

### ActionColumn

Row action buttons for edit, delete, or custom operations:

```php
use Livewire\Tables\Columns\ActionColumn;

ActionColumn::make()
    ->label('Actions')
    ->button(
        label: 'Edit',
        action: fn($row) => "editItem({$row->id})",
        class: 'lt-btn-primary',
    )
    ->button(
        label: 'Delete',
        action: fn($row) => "deleteItem({$row->id})",
        class: 'lt-btn-primary',
        visible: fn($row) => $row->can_delete,
    );
```

#### `button()` Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `label` | `string` | Button text |
| `action` | `Closure` | Returns the `wire:click` expression. Receives `($row, $table)` |
| `class` | `string` | CSS classes for the button |
| `icon` | `string\|null` | Optional SVG/HTML icon prepended to label |
| `visible` | `Closure\|bool` | Per-row visibility. Closure receives `($row)` |

#### Custom Rendering

Override the default button rendering with `render()`:

```php
ActionColumn::make()
    ->render(fn($row, $table) => view('tables.actions', ['item' => $row]));
```

#### Factory Shorthand

```php
use Livewire\Tables\Columns\Column;

Column::actions()
    ->button('Edit', fn($row) => "edit({$row->id})", 'lt-btn-primary')
    ->button('Delete', fn($row) => "delete({$row->id})", 'lt-btn-primary');
```

#### Complete Example

```php
public function columns(): array
{
    return [
        TextColumn::make('name')->sortable()->searchable(),
        TextColumn::make('email')->sortable(),
        ActionColumn::make()
            ->label('Actions')
            ->button(
                label: 'View',
                action: fn($row) => "viewUser({$row->id})",
                class: 'lt-btn-primary',
                icon: '<svg class="w-4 h-4" ...>...</svg>',
            )
            ->button(
                label: 'Delete',
                action: fn($row) => "confirmDelete({$row->id})",
                class: 'lt-btn-primary',
                visible: fn($row) => auth()->user()->can('delete', $row),
            ),
    ];
}

public function viewUser(int $id): void
{
    $this->redirect(route('users.show', $id));
}

public function confirmDelete(int $id): void
{
    User::findOrFail($id)->delete();
}
```

## Common Methods

All column types share these methods:

| Method | Description |
|--------|-------------|
| `label(string)` | Display label in the header |
| `render(Closure)` | Computed cell content from the row model |
| `sortable()` | Enable sorting on this column |
| `searchable()` | Include in global search |
| `searchable(string)` | Search on a specific database column (for joins) |
| `searchable(Closure)` | Custom search callback (BladeColumn) |
| `hidden()` | Hide column by default |
| `hideIf(bool)` | Conditionally hide column |
| `width(string)` | CSS width (e.g., `'200px'`) |
| `format(string\|Closure)` | Transform the displayed value |
| `view(string)` | Use a custom Blade view for rendering |
| `headerClass(string)` | CSS class for the `<th>` |
| `cellClass(string)` | CSS class for the `<td>` |
| `columnClass(string)` | CSS class applied to both `<th>` and `<td>` |
| `key(string)` | Override the internal key |
| `selectAs(string)` | Override the resolution alias for joined columns |

## Factory Shorthands

```php
use Livewire\Tables\Columns\Column;

Column::text('name');        // TextColumn
Column::boolean('active');   // BooleanColumn
Column::date('created_at');  // DateColumn
Column::image('avatar');     // ImageColumn
Column::blade();             // BladeColumn
Column::actions();           // ActionColumn
```

## Joined Columns

For columns from joined tables, use dot notation in `make()`:

```php
TextColumn::make('brands.name')
    ->label('Brand')
    ->sortable()
    ->searchable();
```

This works with your query's JOIN:

```php
public function query(): Builder
{
    return Product::query()
        ->join('brands', 'products.brand_id', '=', 'brands.id')
        ->select('products.*', 'brands.name as brand_name');
}
```

When using an alias, specify the actual database column for search:

```php
TextColumn::make('brand_name')
    ->label('Brand')
    ->sortable()
    ->searchable('brands.name');
```
