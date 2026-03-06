# Join Support

Columns automatically resolve joined table fields. Use dot notation (`table.column`) in `make()` and the engine handles search, sort, and display.

## How It Works

- `make('table.column')` uses the qualified name for WHERE/ORDER BY in SQL
- The display value is resolved using the following priority:
  1. The value of `selectAs()` if explicitly set
  2. The auto-derived alias (`table_column`, replacing `.` with `_`)
  3. **Fallback**: the bare column name (`column`) if the derived alias is not present in the result row
- `selectAs(string)` is available as an optional override for fully custom aliases

## Alias Resolution Reference

| `make()` argument | SELECT alias in query | Works? |
|---|---|---|
| `users.email` | `users.email as users_email` | ✅ (auto-derived match) |
| `users.email` | `users.email as user_email` | ✅ (fallback to bare `email`) |
| `users.email` | *(no alias, join only)* | ✅ (fallback to bare `email`) |
| `user_email` | `users.email as user_email` | ✅ (direct attribute match) |

> **Note on sorting and searching**: The field passed to `make()` is always used as-is for `ORDER BY` and `WHERE` clauses. Dot notation (`users.email`) produces qualified SQL — safe and unambiguous with joins. A plain alias (`user_email`) is used literally in SQL, which may cause ambiguous column errors with joins unless your database can resolve it.

## Basic Example

```php
use Livewire\Tables\Columns\TextColumn;

public function query(): Builder
{
    return Product::query()
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->select('products.*', 'categories.name as category_name');
}

public function columns(): array
{
    return [
        TextColumn::make('products.name')
            ->label('Product'),

        // Works: dot notation → sorts/searches as `categories.name`, display falls back correctly
        TextColumn::make('categories.name')
            ->label('Category')
            ->sortable()
            ->searchable(),
    ];
}
```

The column `categories.name` will:
- Search via `WHERE categories.name LIKE '%term%'`
- Sort via `ORDER BY categories.name ASC`
- Display: tries `categories_name` on the row first; falls back to `name` if not present

## With Multiple Joins

```php
public function query(): Builder
{
    return Order::query()
        ->join('users', 'users.id', '=', 'orders.user_id')
        ->join('products', 'products.id', '=', 'orders.product_id')
        ->select(
            'orders.*',
            'users.name as user_name',
            'products.name as product_name',
        );
}

public function columns(): array
{
    return [
        TextColumn::make('orders.id')->label('Order #'),
        TextColumn::make('users.name')->label('Customer')->sortable()->searchable(),
        TextColumn::make('products.name')->label('Product')->sortable()->searchable(),
    ];
}
```

Here the SELECT uses `user_name` and `product_name` (not `users_name`/`products_name`). The display engine falls back to the bare column name (`name`) from each joined row automatically.

## Key Points

- **Recommended**: always use `table.column` dot notation in `make()` — this ensures unambiguous SQL for sort and search
- **Display resolution** is automatic: `table.column` first tries `table_column`, then falls back to `column`
- **If you SELECT with a custom alias** (e.g. `users.email as user_email`), you can either:
  - Keep using `make('users.email')` — display will resolve through the fallback
  - Or explicitly call `->selectAs('user_email')` for absolute clarity
- **Avoid using plain alias names** like `make('user_email')` when there is a join — while display may work, sorting and searching will use `user_email` literally in SQL which can be ambiguous
