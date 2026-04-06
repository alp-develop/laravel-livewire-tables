<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Columns\DateColumn;
use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Core\Contracts\FilterContract;
use Livewire\Tables\Filters\DateRangeFilter;
use Livewire\Tables\Filters\MultiDateFilter;
use Livewire\Tables\Filters\NumberRangeFilter;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class OrdersTable extends DataTableComponent
{
    public string $tableKey = 'orders';

    public $param;

    public function configure(): void
    {
        $this->setDefaultPerPage(10);
        $this->setSearchDebounce(300);
        $this->setEmptyMessage('No orders found matching your criteria.');

        if ($this->isBootstrap5()) {
            $dark = $this->darkMode;
            $this->setHeadClass($dark ? 'lt-thead-indigo lt-thead-dark' : 'lt-thead-indigo');
            $this->setFilterLabelClass('form-label small mb-1 fw-semibold '.($dark ? 'lt-text-indigo-light' : 'lt-text-indigo'));
            $this->setFilterBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-indigo-subtle lt-btn-dark' : 'lt-btn-indigo-subtle'));
            $this->setFilterBtnActiveClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-indigo-active lt-btn-dark' : 'lt-btn-indigo-active'));
            $this->setColumnBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-indigo-subtle lt-btn-dark' : 'lt-btn-indigo-subtle'));
            $this->setBulkBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium opacity-50 pe-none '.($dark ? 'lt-btn-indigo-subtle lt-btn-dark' : 'lt-btn-indigo-subtle'));
            $this->setBulkBtnActiveClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-indigo-active lt-btn-dark' : 'lt-btn-indigo-active'));
            $this->setRowClass(fn (mixed $row): string => match ($row->status ?? '') {
                'cancelled' => $dark ? 'lt-row-danger-dark' : 'table-danger',
                'delivered' => $dark ? 'lt-row-success-dark' : 'table-success',
                default => '',
            });
        } elseif ($this->isBootstrap4()) {
            $dark = $this->darkMode;
            $this->setHeadClass($dark ? 'lt-thead-indigo lt-thead-dark' : 'lt-thead-indigo');
            $this->setFilterLabelClass('small mb-1 font-weight-bold '.($dark ? 'lt-text-indigo-light' : 'lt-text-indigo'));
            $this->setFilterBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-indigo-subtle lt-btn-dark' : 'lt-btn-indigo-subtle'));
            $this->setFilterBtnActiveClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-indigo-active lt-btn-dark' : 'lt-btn-indigo-active'));
            $this->setColumnBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-indigo-subtle lt-btn-dark' : 'lt-btn-indigo-subtle'));
            $this->setBulkBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold opacity-50 '.($dark ? 'lt-btn-indigo-subtle lt-btn-dark' : 'lt-btn-indigo-subtle'));
            $this->setBulkBtnActiveClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-indigo-active lt-btn-dark' : 'lt-btn-indigo-active'));
            $this->setRowClass(fn (mixed $row): string => match ($row->status ?? '') {
                'cancelled' => $dark ? 'lt-row-danger-dark' : 'table-danger',
                'delivered' => $dark ? 'lt-row-success-dark' : 'table-success',
                default => '',
            });
        } else {
            $dark = $this->darkMode;
            $this->setHeadClass('bg-indigo-50 text-indigo-800'.($dark ? ' dark:bg-indigo-900/20 dark:text-indigo-300' : ''));
            $this->setFilterLabelClass('font-semibold text-indigo-600'.($dark ? ' dark:text-indigo-400' : ''));
            $this->setFilterInputClass('border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500'.($dark ? ' dark:border-indigo-700 dark:focus:border-indigo-500' : ''));
            $this->setFilterBtnClass('inline-flex items-center gap-1.5 rounded-lg border border-indigo-300 bg-white px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50 transition-colors'.($dark ? ' dark:bg-gray-800 dark:border-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/30' : ''));
            $this->setFilterBtnActiveClass('inline-flex items-center gap-1.5 rounded-lg border border-indigo-500 bg-indigo-100 px-3 py-2 text-sm font-medium text-indigo-800 transition-colors'.($dark ? ' dark:bg-indigo-900/30 dark:border-indigo-500 dark:text-indigo-300' : ''));
            $this->setColumnBtnClass('inline-flex items-center gap-1.5 rounded-lg border border-indigo-300 bg-white px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50 transition-colors'.($dark ? ' dark:bg-gray-800 dark:border-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/30' : ''));
            $this->setBulkBtnClass('inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 border border-indigo-200 px-3 py-2 text-sm font-medium text-indigo-300 cursor-default select-none'.($dark ? ' dark:bg-gray-800 dark:border-indigo-800 dark:text-indigo-600' : ''));
            $this->setBulkBtnActiveClass('inline-flex items-center gap-1.5 rounded-lg border border-indigo-500 bg-indigo-100 px-3 py-2 text-sm font-medium text-indigo-800 cursor-pointer hover:bg-indigo-200 transition-colors'.($dark ? ' dark:bg-indigo-900/30 dark:border-indigo-500 dark:text-indigo-300 dark:hover:bg-indigo-900/50' : ''));
            $this->setRowClass(fn (mixed $row): string => match ($row->status ?? '') {
                'cancelled' => 'bg-red-50 text-red-800'.($dark ? ' dark:bg-red-900/20 dark:text-red-300' : ''),
                'delivered' => 'bg-green-50'.($dark ? ' dark:bg-green-900/20' : ''),
                default => '',
            });
        }
    }

    public function query(): Builder
    {
        return Order::query()
            ->join('brands', 'brands.id', '=', 'orders.brand_id')
            ->select(
                'orders.*',
                'orders.id as orders_id',
                'brands.name as brands_name',
                'brands.country as brands_country',
                'brands.tier as brands_tier',
            );
    }

    /** @return array<int, ColumnContract> */
    public function columns(): array
    {
        return [
            TextColumn::make('orders.id')
                ->label('#')
                ->columnClass('col-order-id')
                ->sortable(),

            TextColumn::make('customer_name')
                ->label('Customer')
                ->sortable()
                ->searchable(),

            TextColumn::make('customer_email')
                ->label('Email')
                ->sortable()
                ->searchable(),

            TextColumn::make('product_name')
                ->label('Product')
                ->sortable()
                ->searchable(),

            TextColumn::make('brands.name')
                ->label('Brand2')
                ->sortable()
                ->searchable(),

            TextColumn::make('brands.country')
                ->label('Country')
                ->sortable()
                ->searchable(),

            TextColumn::make('brands.tier')
                ->label('Tier')
                ->columnClass('col-tier')
                ->sortable()
                ->cellClass($this->isBootstrap() ? 'text-capitalize' : 'capitalize'),

            TextColumn::make('quantity')
                ->label('Qty')
                ->sortable()
                ->headerClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->cellClass($this->isBootstrap5() ? 'text-end' : 'text-right'),

            TextColumn::make('unit_price')
                ->label('Unit Price')
                ->sortable()
                ->headerClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->cellClass(match (true) {
                    $this->isBootstrap5() => 'text-end fw-semibold',
                    $this->isBootstrap4() => 'text-right font-weight-bold',
                    default => 'text-right font-semibold',
                })
                ->format(fn (mixed $value) => '$'.number_format((float) $value, 2)),

            TextColumn::make('status')
                ->label('Status')
                ->sortable()
                ->cellClass(match (true) {
                    $this->isBootstrap5() => 'text-capitalize fw-medium',
                    $this->isBootstrap4() => 'text-capitalize font-weight-bold',
                    default => 'capitalize font-medium',
                }),

            DateColumn::make('ordered_at')
                ->label('Date')
                ->sortable()
                ->format('M d, Y'),
        ];
    }

    /** @return array<int, FilterContract> */
    public function filters(): array
    {
        return [
            TextFilter::make('customer_name')
                ->key('customer')
                ->label('Customer Name')
                ->filterClass('filter-customer')
                ->placeholder('Search customer...')
                ->filter(fn (Builder $query, mixed $value) => $query->where('customer_name', 'LIKE', "%{$value}%")),

            SelectFilter::make('status')
                ->label('Status')
                ->labelClass(match (true) {
                    $this->isBootstrap5() => 'text-warning fw-bold',
                    $this->isBootstrap4() => 'text-warning font-weight-bold',
                    default => 'text-orange-600 font-bold',
                })
                ->inputClass($this->isBootstrap() ? '' : 'border-orange-300 focus:border-orange-500')
                ->setOptions([
                    '' => 'All Statuses',
                    'pending' => 'Pending',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('orders.status', $value)),

            SelectFilter::make('brands.tier')
                ->label('Brand Tier')
                ->setOptions([
                    '' => 'All Tiers',
                    'premium' => 'Premium',
                    'standard' => 'Standard',
                    'budget' => 'Budget',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('brands.tier', $value)),

            SelectFilter::make('brands.country')
                ->label('Brand Country')
                ->setOptions([
                    '' => 'All Countries',
                    'USA' => 'USA',
                    'South Korea' => 'South Korea',
                    'Japan' => 'Japan',
                    'Switzerland' => 'Switzerland',
                    'Singapore' => 'Singapore',
                    'UK' => 'UK',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('brands.country', $value)),

            NumberRangeFilter::make('unit_price')
                ->label('Price Range')
                ->min(0.0)
                ->max(9999.99)
                ->step(0.01)
                ->filter(function (Builder $query, mixed $value): Builder {
                    if (isset($value['min']) && $value['min'] !== '') {
                        $query->where('orders.unit_price', '>=', (float) $value['min']);
                    }
                    if (isset($value['max']) && $value['max'] !== '') {
                        $query->where('orders.unit_price', '<=', (float) $value['max']);
                    }

                    return $query;
                }),

            SelectFilter::make('status_multi')
                ->key('statuses')
                ->label('Statuses (multi)')
                ->multiple()
                ->searchable()
                ->initialValue(['pending', 'shipped'])
                ->setOptions([
                    'pending' => 'Pending',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->whereIn('orders.status', $value)),

            MultiDateFilter::make('ordered_at')
                ->key('order_dates')
                ->label('Specific Order Dates')
                ->minDate('2023-01-01')
                ->maxDate('2027-12-31')
                ->filter(function (Builder $query, mixed $value): Builder {
                    return $query->where(function (Builder $q) use ($value): void {
                        foreach ($value as $date) {
                            $q->orWhereDate('orders.ordered_at', $date);
                        }
                    });
                }),

            DateRangeFilter::make('ordered_at_range')
                ->key('order_range')
                ->label('Order Date Range')
                ->format('Y-m-d')
                ->minDate('2023-01-01')
                ->maxDate('2027-12-31')
                ->filter(function (Builder $query, mixed $value): Builder {
                    if (isset($value['from']) && $value['from'] !== '') {
                        $query->whereDate('orders.ordered_at', '>=', $value['from']);
                    }
                    if (isset($value['to']) && $value['to'] !== '') {
                        $query->whereDate('orders.ordered_at', '<=', $value['to']);
                    }

                    return $query;
                }),
        ];
    }
}
