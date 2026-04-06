<?php

namespace App\Livewire;

use App\Models\CatalogItem;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Columns\BladeColumn;
use Livewire\Tables\Columns\ImageColumn;
use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Core\Contracts\FilterContract;
use Livewire\Tables\Filters\NumberRangeFilter;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Livewire\DataTableComponent;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogTable extends DataTableComponent
{
    public string $tableKey = 'catalog';

    public function configure(): void
    {
        $this->setDefaultPerPage(25);
        $this->setSearchDebounce(300);
        $this->setEmptyMessage('No catalog items found.');
        if ($this->isBootstrap5()) {
            $dark = $this->darkMode;
            $this->setHeadClass($dark ? 'lt-thead-sky lt-thead-dark' : 'lt-thead-sky');
            $this->setFilterLabelClass('form-label small mb-1 fw-semibold '.($dark ? 'lt-text-sky-light' : 'lt-text-sky'));
            $this->setFilterBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-sky-subtle lt-btn-dark' : 'lt-btn-sky-subtle'));
            $this->setFilterBtnActiveClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-sky-active lt-btn-dark' : 'lt-btn-sky-active'));
            $this->setColumnBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-sky-subtle lt-btn-dark' : 'lt-btn-sky-subtle'));
            $this->setBulkBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium opacity-50 pe-none '.($dark ? 'lt-btn-sky-subtle lt-btn-dark' : 'lt-btn-sky-subtle'));
            $this->setBulkBtnActiveClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-sky-active lt-btn-dark' : 'lt-btn-sky-active'));
        } elseif ($this->isBootstrap4()) {
            $dark = $this->darkMode;
            $this->setHeadClass($dark ? 'lt-thead-sky lt-thead-dark' : 'lt-thead-sky');
            $this->setFilterLabelClass('small mb-1 font-weight-bold '.($dark ? 'lt-text-sky-light' : 'lt-text-sky'));
            $this->setFilterBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-sky-subtle lt-btn-dark' : 'lt-btn-sky-subtle'));
            $this->setFilterBtnActiveClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-sky-active lt-btn-dark' : 'lt-btn-sky-active'));
            $this->setColumnBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-sky-subtle lt-btn-dark' : 'lt-btn-sky-subtle'));
            $this->setBulkBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold opacity-50 '.($dark ? 'lt-btn-sky-subtle lt-btn-dark' : 'lt-btn-sky-subtle'));
            $this->setBulkBtnActiveClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-sky-active lt-btn-dark' : 'lt-btn-sky-active'));
        } else {
            $dark = $this->darkMode;
            $this->setHeadClass('bg-sky-50 text-sky-800'.($dark ? ' dark:bg-sky-900/20 dark:text-sky-300' : ''));
            $this->setFilterLabelClass('font-semibold text-sky-700'.($dark ? ' dark:text-sky-400' : ''));
            $this->setFilterBtnClass('inline-flex items-center gap-1.5 rounded-lg border border-sky-300 bg-white px-3 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 transition-colors'.($dark ? ' dark:bg-gray-800 dark:border-sky-700 dark:text-sky-400 dark:hover:bg-sky-900/30' : ''));
            $this->setFilterBtnActiveClass('inline-flex items-center gap-1.5 rounded-lg border border-sky-500 bg-sky-100 px-3 py-2 text-sm font-medium text-sky-800 transition-colors'.($dark ? ' dark:bg-sky-900/30 dark:border-sky-500 dark:text-sky-300' : ''));
            $this->setColumnBtnClass('inline-flex items-center gap-1.5 rounded-lg border border-sky-300 bg-white px-3 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 transition-colors'.($dark ? ' dark:bg-gray-800 dark:border-sky-700 dark:text-sky-400 dark:hover:bg-sky-900/30' : ''));
            $this->setBulkBtnClass('inline-flex items-center gap-1.5 rounded-lg bg-sky-50 border border-sky-200 px-3 py-2 text-sm font-medium text-sky-300 cursor-default select-none'.($dark ? ' dark:bg-gray-800 dark:border-sky-800 dark:text-sky-600' : ''));
            $this->setBulkBtnActiveClass('inline-flex items-center gap-1.5 rounded-lg border border-sky-500 bg-sky-100 px-3 py-2 text-sm font-medium text-sky-800 cursor-pointer hover:bg-sky-200 transition-colors'.($dark ? ' dark:bg-sky-900/30 dark:border-sky-500 dark:text-sky-300 dark:hover:bg-sky-900/50' : ''));
        }
    }

    public function query(): Builder
    {
        return CatalogItem::query();
    }

    /** @return array<string, string> */
    public function bulkActions(): array
    {
        return [
            'exportCsv' => 'Export CSV',
        ];
    }

    public function toggleActive(int $id): void
    {
        $item = CatalogItem::findOrFail($id);
        $item->update(['active' => ! $item->active]);
    }

    public function exportCsv(): StreamedResponse
    {
        $ids = $this->getSelectedIds();

        return response()->streamDownload(function () use ($ids): void {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['ID', 'SKU', 'Name', 'Category', 'Brand', 'Price', 'Stock', 'Rating', 'Country', 'Active']);

            CatalogItem::whereIn('id', $ids)
                ->orderBy('id')
                ->chunk(500, function ($items) use ($out): void {
                    foreach ($items as $item) {
                        fputcsv($out, [
                            $item->id,
                            $item->sku,
                            $item->name,
                            $item->category,
                            $item->brand,
                            number_format((float) $item->price, 2),
                            $item->stock,
                            $item->rating,
                            $item->country,
                            $item->active ? 'Yes' : 'No',
                        ]);
                    }
                });

            fclose($out);
        }, 'catalog-export-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /** @return array<int, ColumnContract> */
    public function columns(): array
    {
        return [
            ImageColumn::make('image_url')
                ->label('Photo')
                ->alt('name')
                ->dimensions(48, 48)
                ->width('64px'),

            TextColumn::make('id')
                ->label('#')
                ->sortable()
                ->width('60px')
                ->headerClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->cellClass(match (true) {
                    $this->isBootstrap5() => 'text-end text-muted',
                    $this->isBootstrap4() => 'text-right text-muted',
                    default => 'text-right text-gray-400',
                }),

            TextColumn::make('sku')
                ->label('SKU')
                ->sortable()
                ->searchable()
                ->cellClass(match (true) {
                    $this->isBootstrap5() => 'font-monospace small text-muted',
                    $this->isBootstrap4() => 'small text-muted',
                    default => 'font-mono text-xs text-gray-500',
                }),

            TextColumn::make('name')
                ->label('Name')
                ->sortable()
                ->searchable(),

            TextColumn::make('brand')
                ->label('Brand')
                ->sortable()
                ->searchable(),

            TextColumn::make('category')
                ->label('Category')
                ->sortable(),

            TextColumn::make('price')
                ->label('Price')
                ->sortable()
                ->headerClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->cellClass(match (true) {
                    $this->isBootstrap5() => 'text-end fw-semibold',
                    $this->isBootstrap4() => 'text-right font-weight-bold',
                    default => 'text-right font-semibold',
                })
                ->format(fn (mixed $value) => '$'.number_format((float) $value, 2)),

            TextColumn::make('rating')
                ->label('Rating')
                ->sortable()
                ->headerClass('text-center')
                ->cellClass('text-center')
                ->format(fn (mixed $value) => number_format((float) $value, 1).' ★'),

            TextColumn::make('stock')
                ->label('Stock')
                ->sortable()
                ->headerClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->cellClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->format(fn (mixed $value) => number_format((int) $value)),

            TextColumn::make('country')
                ->label('Country')
                ->sortable(),

            TextColumn::make()
                ->render(fn (CatalogItem $row) => $row->brand.' - '.$row->category)
                ->label('Info')
                ->cellClass(match (true) {
                    $this->isBootstrap5() => 'text-muted small',
                    $this->isBootstrap4() => 'text-muted small',
                    default => 'text-gray-500 text-sm',
                }),

            BladeColumn::make()
                ->label('Status')
                ->searchable(fn (Builder $query, string $search) => $query
                    ->orWhereRaw("CASE WHEN active = 1 THEN 'active' ELSE 'inactive' END LIKE ?", ["{$search}%"]))
                ->render(fn (CatalogItem $row) => view('tables.catalog-row-actions', ['item' => $row])),
        ];
    }

    /** @return array<int, FilterContract> */
    public function filters(): array
    {
        return [
            TextFilter::make('name')
                ->label('Name')
                ->placeholder('Search name...')
                ->filter(fn (Builder $query, mixed $value) => $query->where('name', 'LIKE', "%{$value}%")),

            TextFilter::make('sku')
                ->label('SKU')
                ->placeholder('Search SKU...')
                ->filter(fn (Builder $query, mixed $value) => $query->where('sku', 'LIKE', "%{$value}%")),

            SelectFilter::make('category')
                ->label('Category')
                ->setOptions([
                    '' => 'All Categories',
                    'Electronics' => 'Electronics',
                    'Clothing' => 'Clothing',
                    'Food & Drinks' => 'Food & Drinks',
                    'Home & Garden' => 'Home & Garden',
                    'Sports' => 'Sports',
                    'Books' => 'Books',
                    'Toys & Games' => 'Toys & Games',
                    'Beauty' => 'Beauty',
                    'Automotive' => 'Automotive',
                    'Pet Supplies' => 'Pet Supplies',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('category', $value)),

            SelectFilter::make('country')
                ->label('Country')
                ->setOptions([
                    '' => 'All Countries',
                    'USA' => 'USA',
                    'Germany' => 'Germany',
                    'Japan' => 'Japan',
                    'China' => 'China',
                    'France' => 'France',
                    'Italy' => 'Italy',
                    'Spain' => 'Spain',
                    'UK' => 'UK',
                    'Canada' => 'Canada',
                    'Australia' => 'Australia',
                    'Brazil' => 'Brazil',
                    'Mexico' => 'Mexico',
                    'South Korea' => 'South Korea',
                    'India' => 'India',
                    'Netherlands' => 'Netherlands',
                    'Sweden' => 'Sweden',
                    'Switzerland' => 'Switzerland',
                    'Denmark' => 'Denmark',
                    'Poland' => 'Poland',
                    'Portugal' => 'Portugal',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('country', $value)),

            SelectFilter::make('active')
                ->label('Status')
                ->setOptions([
                    '' => 'All',
                    '1' => 'Active',
                    '0' => 'Inactive',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('active', (bool) $value)),

            NumberRangeFilter::make('price')
                ->label('Price Range ($)')
                ->min(0.0)
                ->max(1500.0)
                ->step(10.0)
                ->filter(function (Builder $query, mixed $value): Builder {
                    if (isset($value['min']) && $value['min'] !== '') {
                        $query->where('price', '>=', (float) $value['min']);
                    }
                    if (isset($value['max']) && $value['max'] !== '') {
                        $query->where('price', '<=', (float) $value['max']);
                    }

                    return $query;
                }),

            NumberRangeFilter::make('rating')
                ->label('Rating')
                ->min(1.0)
                ->max(5.0)
                ->step(0.5)
                ->filter(function (Builder $query, mixed $value): Builder {
                    if (isset($value['min']) && $value['min'] !== '') {
                        $query->where('rating', '>=', (float) $value['min']);
                    }
                    if (isset($value['max']) && $value['max'] !== '') {
                        $query->where('rating', '<=', (float) $value['max']);
                    }

                    return $query;
                }),
        ];
    }
}
