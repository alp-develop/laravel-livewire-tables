# Join Support

Columns automatically resolve joined table fields. Use dot notation (`table.column`) in `make()` and the engine handles search, sort, and display.

## How It Works

- `make('table.column')` uses the qualified name for WHERE/ORDER BY in SQL
- The display value is resolved using the alias (`table_column`) or the query's `AS` alias
- `selectAs(string)` is available as an optional override for custom aliases

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
- Display: resolves the value from `category_name` on the result row

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

## Key Points

- Always `->select(...)` in your `query()` to include joined columns with aliases
- Use `table.column` in `make()` so sort/search queries are unambiguous
- Display values are auto-resolved: `table.column` looks for `table_column` on the result
- `selectAs()` is optional — only use when the auto-resolved alias doesn't match
