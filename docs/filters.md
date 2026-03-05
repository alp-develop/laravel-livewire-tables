# Filters

Filters allow users to narrow down table data. All filters extend the base `Filter` class and implement `FilterContract`.

## Filter Types

| Type | Class | Description |
|------|-------|-------------|
| Text | `TextFilter` | Free text input (`LIKE` search) |
| Select | `SelectFilter` | Dropdown with options |
| Boolean | `BooleanFilter` | True/false toggle |
| Number | `NumberFilter` | Numeric input with min/max/step |
| Number Range | `NumberRangeFilter` | Min/max number range |
| Date | `DateFilter` | Single date picker |
| Date Range | `DateRangeFilter` | From/to date range (Flatpickr) |
| Multi Date | `MultiDateFilter` | Multiple date selection (Flatpickr) |

## Defining Filters

Override the `filters()` method in your table component:

```php
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\BooleanFilter;

public function filters(): array
{
    return [
        TextFilter::make('name')
            ->label('Name')
            ->placeholder('Search by name...'),

        SelectFilter::make('status')
            ->label('Status')
            ->setOptions([
                'active'   => 'Active',
                'inactive' => 'Inactive',
                'pending'  => 'Pending',
            ]),

        BooleanFilter::make('verified')
            ->label('Verified'),
    ];
}
```

## Filter Types in Detail

### TextFilter

Applies a `LIKE` query on the field:

```php
TextFilter::make('email')
    ->label('Email')
    ->placeholder('Filter by email...');
```

### SelectFilter

Dropdown with predefined options:

```php
SelectFilter::make('role')
    ->label('Role')
    ->setOptions([
        'admin'  => 'Admin',
        'editor' => 'Editor',
        'viewer' => 'Viewer',
    ]);
```

**Multiple selection:**

```php
SelectFilter::make('tags')
    ->label('Tags')
    ->setOptions($tagOptions)
    ->multiple();
```

**Searchable dropdown:**

Renders an Alpine-powered dropdown with text search instead of a native `<select>`:

```php
SelectFilter::make('country')
    ->label('Country')
    ->setOptions($countries)
    ->searchable();
```

**Searchable + multiple:**

```php
SelectFilter::make('tags')
    ->label('Tags')
    ->setOptions($tagOptions)
    ->multiple()
    ->searchable();
```

> Note: `searchable()` on filters with `parent()` dependency is not supported — dependent filters use a native `<select>` that updates automatically when the parent value changes.

### Dependent Filters (Parent-Child)

Create cascading filters where the child's options depend on the parent's value:

```php
SelectFilter::make('country')
    ->label('Country')
    ->setOptions($countries),

SelectFilter::make('city')
    ->label('City')
    ->parent('country')
    ->parentFilter(fn(mixed $countryId) => City::where('country_id', $countryId)
        ->pluck('name', 'id')
        ->toArray()),
```

The `city` filter options reload automatically when the `country` value changes. When the parent changes, the child filter resets to empty.

#### How It Works

1. `parent('country')` — sets the dependency on the `country` filter key
2. `parentFilter(Closure)` — receives the parent's current value and returns an options array
3. When the parent value changes, `updatedTableFilters` resets the child filter
4. The child `<select>` re-renders with the new options from `resolveOptions()`

#### Multi-Level Dependencies

Chain multiple levels of dependent filters:

```php
SelectFilter::make('country')->label('Country')
    ->setOptions($countries),

SelectFilter::make('state')->label('State')
    ->parent('country')
    ->parentFilter(fn($countryId) => State::where('country_id', $countryId)->pluck('name', 'id')->toArray()),

SelectFilter::make('city')->label('City')
    ->parent('state')
    ->parentFilter(fn($stateId) => City::where('state_id', $stateId)->pluck('name', 'id')->toArray()),
```

### BooleanFilter

Simple true/false toggle:

```php
BooleanFilter::make('active')
    ->label('Active Only');
```

### NumberFilter

Numeric input with optional bounds:

```php
NumberFilter::make('price')
    ->label('Price')
    ->min(0)
    ->max(10000)
    ->step(0.01);
```

Values are automatically clamped to min/max bounds.

### NumberRangeFilter

Two inputs for min/max range:

```php
NumberRangeFilter::make('age')
    ->label('Age Range')
    ->min(18)
    ->max(100)
    ->step(1);
```

### DateFilter

Single date selection:

```php
DateFilter::make('created_at')
    ->label('Created On')
    ->minDate('2020-01-01')
    ->maxDate('2030-12-31');
```

### DateRangeFilter

From/to date range (uses Flatpickr):

```php
DateRangeFilter::make('created_at')
    ->label('Created Between')
    ->format('Y-m-d')
    ->minDate('2020-01-01')
    ->maxDate('2030-12-31')
    ->calendarClass('custom-calendar');
```

### MultiDateFilter

Select multiple individual dates (uses Flatpickr):

```php
MultiDateFilter::make('event_dates')
    ->label('Event Dates')
    ->format('Y-m-d')
    ->minDate('2024-01-01')
    ->maxDate('2025-12-31');
```

## Custom Filter Logic

Use the `filter()` method to override the default query behavior:

```php
TextFilter::make('full_name')
    ->label('Name')
    ->filter(fn(Builder $query, mixed $value) => $query
        ->where('first_name', 'LIKE', "%{$value}%")
        ->orWhere('last_name', 'LIKE', "%{$value}%")),
```

Works on any filter type:

```php
SelectFilter::make('status')
    ->label('Status')
    ->setOptions(['recent' => 'Recent', 'old' => 'Old'])
    ->filter(fn(Builder $query, mixed $value) => match($value) {
        'recent' => $query->where('created_at', '>=', now()->subDays(30)),
        'old'    => $query->where('created_at', '<', now()->subDays(30)),
        default  => $query,
    }),
```

## Initial Values

Set a default initial value for a filter:

```php
SelectFilter::make('status')
    ->label('Status')
    ->setOptions(['active' => 'Active', 'inactive' => 'Inactive'])
    ->initialValue('active'),
```

The filter will be pre-applied when the table first loads.

## Common Methods

| Method | Description |
|--------|-------------|
| `label(string)` | Display label |
| `key(string)` | Override the internal key |
| `placeholder(string)` | Placeholder text |
| `default(mixed)` | Default value |
| `initialValue(mixed)` | Pre-applied initial value |
| `filter(Closure)` | Custom query callback |
| `groupClass(string)` | CSS class for filter wrapper |
| `labelClass(string)` | CSS class for label |
| `inputClass(string)` | CSS class for input element |
| `filterClass(string)` | Alias for `groupClass()` |

## Active Filters

Active filters are displayed as chips in the toolbar. Users can remove individual filters by clicking the chip's remove button, or clear all filters at once.

```php
$applied = $this->getAppliedFilters();
```
