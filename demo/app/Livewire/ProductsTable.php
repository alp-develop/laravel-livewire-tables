<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Columns\BooleanColumn;
use Livewire\Tables\Columns\DateColumn;
use Livewire\Tables\Columns\ImageColumn;
use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Filters\BooleanFilter;
use Livewire\Tables\Filters\DateFilter;
use Livewire\Tables\Filters\DateRangeFilter;
use Livewire\Tables\Filters\MultiDateFilter;
use Livewire\Tables\Filters\NumberFilter;
use Livewire\Tables\Filters\NumberRangeFilter;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class ProductsTable extends DataTableComponent
{
    public string $tableKey = 'products';

    public function configure(): void
    {
        $this->setDefaultPerPage(10);
        $this->setSearchDebounce(300);
        $this->setEmptyMessage('No products found matching your criteria.');

        if ($this->isBootstrap()) {
            $dark = $this->darkMode;
            $bold = $this->isBootstrap5() ? 'fw-semibold' : 'font-weight-bold';
            $this->setHeadClass($dark ? 'lt-thead-tinted' : 'bg-light text-secondary');
            $this->setBodyClass($dark ? 'border-top border-secondary' : 'border-top');
            $this->setRowClass(fn (mixed $row): string => ($row->stock ?? 0) < 5
                ? ($dark ? 'lt-row-danger-dark' : 'table-danger')
                : '');
            $this->setFilterLabelClass($dark ? "$bold lt-text-indigo" : "$bold text-primary");
            $this->setFilterGroupClass($dark ? 'border-bottom border-secondary pb-3' : 'border-bottom pb-3');
        } else {
            $dark = $this->darkMode;
            $this->setHeadClass('bg-gray-50 text-gray-700'.($dark ? ' dark:bg-gray-800/60 dark:text-gray-300' : ''));
            $this->setBodyClass('divide-y divide-gray-200'.($dark ? ' dark:divide-gray-700' : ''));
            $this->setRowClass(fn (mixed $row): string => ($row->stock ?? 0) < 5
                ? ('bg-red-50 text-red-900'.($dark ? ' dark:bg-red-900/20 dark:text-red-300' : ''))
                : '');
            $this->setFilterLabelClass('font-semibold text-indigo-700'.($dark ? ' dark:text-indigo-400' : ''));
            $this->setFilterGroupClass('border-b border-gray-100 pb-3'.($dark ? ' dark:border-gray-700' : ''));
        }
    }

    public function query(): Builder
    {
        return Product::query();
    }

    /** @return array<int, ColumnContract> */
    public function columns(): array
    {
        return [
            ImageColumn::make('image_url')
                ->label('Image')
                ->dimensions(80, 80)
                ->alt('name'),

            TextColumn::make('name')
                ->label('Product')
                ->sortable()
                ->searchable(),

            TextColumn::make('sku')
                ->label('SKU')
                ->columnClass('col-sku')
                ->sortable()
                ->searchable(),

            TextColumn::make('category')
                ->label('Category')
                ->sortable()
                ->searchable(),

            TextColumn::make('subcategory')
                ->label('Subcategory')
                ->sortable()
                ->searchable(),

            TextColumn::make('price')
                ->label('Price')
                ->columnClass('col-price')
                ->sortable()
                ->headerClass(match (true) {
                    $this->isBootstrap5() => 'text-end bg-success bg-opacity-10 text-success',
                    $this->isBootstrap4() => 'text-right lt-bg-success-soft text-success',
                    default => 'text-right bg-green-50 text-green-700',
                })
                ->cellClass(match (true) {
                    $this->isBootstrap5() => 'text-end fw-semibold',
                    $this->isBootstrap4() => 'text-right font-weight-bold',
                    default => 'text-right font-semibold',
                })
                ->format(fn (mixed $value) => '$'.number_format((float) $value, 2)),

            TextColumn::make('stock')
                ->label('Stock')
                ->columnClass('col-stock')
                ->sortable()
                ->headerClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->cellClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->format(fn (mixed $value) => number_format((int) $value)),

            BooleanColumn::make('active')
                ->label('Status')
                ->sortable()
                ->labels('Active', 'Inactive'),

            DateColumn::make('release_date')
                ->label('Released')
                ->sortable()
                ->format('M d, Y'),
        ];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('name')
                ->label('Product Name')
                ->filterClass('filter-name')
                ->placeholder('Filter by name...')
                ->filter(fn (Builder $query, mixed $value) => $query->where('name', 'LIKE', "%{$value}%")),

            SelectFilter::make('category')
                ->key('cat')
                ->label('Category')
                ->searchable()
                ->setOptions([
                    '' => 'All Categories',
                    'Laptops' => 'Laptops',
                    'Phones' => 'Phones',
                    'Tablets' => 'Tablets',
                    'Audio' => 'Audio',
                    'Wearables' => 'Wearables',
                    'TVs' => 'TVs',
                    'Accessories' => 'Accessories',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('category', $value)),

            SelectFilter::make('subcategory')
                ->key('subcat')
                ->label('Subcategory')
                ->parent('cat')
                ->placeholder('Select a subcategory...')
                ->parentFilter(function (mixed $value): array {
                    $map = [
                        'Laptops' => ['Business Laptops' => 'Business Laptops', 'Ultrabooks' => 'Ultrabooks', 'Gaming Laptops' => 'Gaming Laptops'],
                        'Phones' => ['Flagship' => 'Flagship', 'Mid-range' => 'Mid-range'],
                        'Tablets' => ['iPad' => 'iPad', 'Android Tablets' => 'Android Tablets', 'E-readers' => 'E-readers'],
                        'Audio' => ['Headphones' => 'Headphones', 'Earbuds' => 'Earbuds', 'Speakers' => 'Speakers'],
                        'Wearables' => ['Smartwatches' => 'Smartwatches', 'VR Headsets' => 'VR Headsets', 'Gaming Handhelds' => 'Gaming Handhelds'],
                        'TVs' => ['OLED TVs' => 'OLED TVs', 'QLED TVs' => 'QLED TVs'],
                        'Accessories' => ['Peripherals' => 'Peripherals', 'Docking Stations' => 'Docking Stations'],
                    ];

                    return $map[$value];
                })
                ->filter(fn (Builder $query, mixed $value) => $query->where('subcategory', $value)),

            BooleanFilter::make('active')
                ->label('Status')
                ->labelClass(match (true) {
                    $this->isBootstrap5() => 'text-success fw-bold',
                    $this->isBootstrap4() => 'text-success font-weight-bold',
                    default => 'text-emerald-700 font-bold',
                })
                ->inputClass($this->isBootstrap() ? '' : 'border-emerald-400 focus:border-emerald-600 focus:ring-emerald-500')
                ->filter(fn (Builder $query, mixed $value) => $query->where('active', (bool) $value)),

            NumberRangeFilter::make('price')
                ->label('Price Range')
                ->filterClass('filter-price')
                ->labelClass(match (true) {
                    $this->isBootstrap5() => 'text-success fw-bold',
                    $this->isBootstrap4() => 'text-success font-weight-bold',
                    default => 'text-green-700 font-bold',
                })
                ->inputClass($this->isBootstrap() ? '' : 'border-green-400 focus:border-green-600')
                ->min(0.0)
                ->max(9999.99)
                ->step(0.01)
                ->filter(function (Builder $query, mixed $value): Builder {
                    if (isset($value['min']) && $value['min'] !== '') {
                        $query->where('price', '>=', (float) $value['min']);
                    }
                    if (isset($value['max']) && $value['max'] !== '') {
                        $query->where('price', '<=', (float) $value['max']);
                    }

                    return $query;
                }),

            NumberFilter::make('stock')
                ->label('Min Stock')
                ->placeholder('Minimum stock...')
                ->min(0.0)
                ->max(999.0)
                ->step(1.0)
                ->filter(fn (Builder $query, mixed $value) => $query->where('stock', '>=', (int) $value)),

            DateFilter::make('release_date')
                ->label('Released On (exact)')
                ->filter(fn (Builder $query, mixed $value) => $query->whereDate('release_date', $value)),

            DateRangeFilter::make('release_date_range')
                ->label('Release Date Range')
                ->format('Y-m-d')
                ->minDate('2020-01-01')
                ->maxDate('2027-12-31')
                ->calendarClass('cal-release')
                ->filter(function (Builder $query, mixed $value): Builder {
                    if (isset($value['from']) && $value['from'] !== '') {
                        $query->whereDate('release_date', '>=', $value['from']);
                    }
                    if (isset($value['to']) && $value['to'] !== '') {
                        $query->whereDate('release_date', '<=', $value['to']);
                    }

                    return $query;
                }),

            SelectFilter::make('category_multi')
                ->key('cats')
                ->label('Categories (multi)')
                ->multiple()
                ->searchable()
                ->setOptions([
                    'Laptops' => 'Laptops',
                    'Phones' => 'Phones',
                    'Tablets' => 'Tablets',
                    'Audio' => 'Audio',
                    'Wearables' => 'Wearables',
                    'TVs' => 'TVs',
                    'Accessories' => 'Accessories',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->whereIn('category', $value)),

            MultiDateFilter::make('release_date')
                ->key('rel_dates')
                ->label('Specific Release Dates')
                ->minDate('2020-01-01')
                ->maxDate('2027-12-31')
                ->filter(function (Builder $query, mixed $value): Builder {
                    return $query->where(function (Builder $q) use ($value): void {
                        foreach ($value as $date) {
                            $q->orWhereDate('release_date', $date);
                        }
                    });
                }),
        ];
    }
}
