<?php

namespace App\Livewire;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Core\Contracts\FilterContract;
use Livewire\Tables\Filters\NumberRangeFilter;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class PerformanceTable extends DataTableComponent
{
    public string $tableKey = 'performance';

    public function configure(): void
    {
        $this->setDefaultPerPage(25);
        $this->setSearchDebounce(300);
        $this->setEmptyMessage('No employees found.');

        if ($this->isBootstrap5()) {
            $dark = $this->darkMode;
            $this->setHeadClass($dark ? 'lt-thead-violet lt-thead-dark' : 'lt-thead-violet');
            $this->setFilterLabelClass('form-label small mb-1 fw-semibold '.($dark ? 'lt-text-violet-light' : 'lt-text-violet'));
            $this->setFilterBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-violet-subtle lt-btn-dark' : 'lt-btn-violet-subtle'));
            $this->setFilterBtnActiveClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-violet-active lt-btn-dark' : 'lt-btn-violet-active'));
            $this->setColumnBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-violet-subtle lt-btn-dark' : 'lt-btn-violet-subtle'));
            $this->setBulkBtnClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium opacity-50 pe-none '.($dark ? 'lt-btn-violet-subtle lt-btn-dark' : 'lt-btn-violet-subtle'));
            $this->setBulkBtnActiveClass('btn rounded-3 d-inline-flex align-items-center gap-1 fw-medium '.($dark ? 'lt-btn-violet-active lt-btn-dark' : 'lt-btn-violet-active'));
        } elseif ($this->isBootstrap4()) {
            $dark = $this->darkMode;
            $this->setHeadClass($dark ? 'lt-thead-violet lt-thead-dark' : 'lt-thead-violet');
            $this->setFilterLabelClass('small mb-1 font-weight-bold '.($dark ? 'lt-text-violet-light' : 'lt-text-violet'));
            $this->setFilterBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-violet-subtle lt-btn-dark' : 'lt-btn-violet-subtle'));
            $this->setFilterBtnActiveClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-violet-active lt-btn-dark' : 'lt-btn-violet-active'));
            $this->setColumnBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-violet-subtle lt-btn-dark' : 'lt-btn-violet-subtle'));
            $this->setBulkBtnClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold opacity-50 '.($dark ? 'lt-btn-violet-subtle lt-btn-dark' : 'lt-btn-violet-subtle'));
            $this->setBulkBtnActiveClass('btn rounded d-inline-flex align-items-center lt-flex-gap-1 font-weight-bold '.($dark ? 'lt-btn-violet-active lt-btn-dark' : 'lt-btn-violet-active'));
        } else {
            $dark = $this->darkMode;
            $this->setHeadClass('bg-violet-50 text-violet-800'.($dark ? ' dark:bg-violet-900/20 dark:text-violet-300' : ''));
            $this->setFilterLabelClass('font-semibold text-violet-600'.($dark ? ' dark:text-violet-400' : ''));
            $this->setFilterBtnClass('inline-flex items-center gap-1.5 rounded-lg border border-violet-300 bg-white px-3 py-2 text-sm font-medium text-violet-700 hover:bg-violet-50 transition-colors'.($dark ? ' dark:bg-gray-800 dark:border-violet-700 dark:text-violet-400 dark:hover:bg-violet-900/30' : ''));
            $this->setFilterBtnActiveClass('inline-flex items-center gap-1.5 rounded-lg border border-violet-500 bg-violet-100 px-3 py-2 text-sm font-medium text-violet-800 transition-colors'.($dark ? ' dark:bg-violet-900/30 dark:border-violet-500 dark:text-violet-300' : ''));
            $this->setColumnBtnClass('inline-flex items-center gap-1.5 rounded-lg border border-violet-300 bg-white px-3 py-2 text-sm font-medium text-violet-700 hover:bg-violet-50 transition-colors'.($dark ? ' dark:bg-gray-800 dark:border-violet-700 dark:text-violet-400 dark:hover:bg-violet-900/30' : ''));
            $this->setBulkBtnClass('inline-flex items-center gap-1.5 rounded-lg bg-violet-50 border border-violet-200 px-3 py-2 text-sm font-medium text-violet-300 cursor-default select-none'.($dark ? ' dark:bg-gray-800 dark:border-violet-800 dark:text-violet-600' : ''));
            $this->setBulkBtnActiveClass('inline-flex items-center gap-1.5 rounded-lg border border-violet-500 bg-violet-100 px-3 py-2 text-sm font-medium text-violet-800 cursor-pointer hover:bg-violet-200 transition-colors'.($dark ? ' dark:bg-violet-900/30 dark:border-violet-500 dark:text-violet-300 dark:hover:bg-violet-900/50' : ''));
        }
    }

    public function query(): Builder
    {
        return Employee::query();
    }

    /** @return array<string, string> */
    public function bulkActions(): array
    {
        return [
            'activate' => 'Mark Active',
            'deactivate' => 'Mark Inactive',
            'delete' => 'Delete',
        ];
    }

    public function activate(): void
    {
        Employee::whereIn('id', $this->getSelectedIds())->update(['status' => 'active']);
    }

    public function deactivate(): void
    {
        Employee::whereIn('id', $this->getSelectedIds())->update(['status' => 'inactive']);
    }

    public function delete(): void
    {
        Employee::whereIn('id', $this->getSelectedIds())->delete();
    }

    /** @return array<int, ColumnContract> */
    public function columns(): array
    {
        return [
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

            TextColumn::make('name')
                ->label('Name')
                ->sortable()
                ->searchable(),

            TextColumn::make('email')
                ->label('Email')
                ->sortable()
                ->searchable(),

            TextColumn::make('department')
                ->label('Department')
                ->sortable()
                ->searchable(),

            TextColumn::make('position')
                ->label('Position')
                ->sortable()
                ->searchable(),

            TextColumn::make('salary')
                ->label('Salary')
                ->sortable()
                ->headerClass($this->isBootstrap5() ? 'text-end' : 'text-right')
                ->cellClass(match (true) {
                    $this->isBootstrap5() => 'text-end fw-semibold',
                    $this->isBootstrap4() => 'text-right font-weight-bold',
                    default => 'text-right font-semibold',
                })
                ->format(fn (mixed $value) => '$'.number_format((float) $value, 0)),

            TextColumn::make('status')
                ->label('Status')
                ->sortable()
                ->cellClass($this->isBootstrap() ? 'text-capitalize' : 'capitalize')
                ->format(fn (mixed $value) => $value),

            TextColumn::make('hire_date')
                ->label('Hired')
                ->sortable()
                ->format(fn (mixed $value) => $value ? Carbon::parse($value)->format('M Y') : '-'),
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

            SelectFilter::make('department')
                ->label('Department')
                ->setOptions([
                    '' => 'All Departments',
                    'Engineering' => 'Engineering',
                    'Marketing' => 'Marketing',
                    'Sales' => 'Sales',
                    'Finance' => 'Finance',
                    'HR' => 'HR',
                    'Operations' => 'Operations',
                    'Design' => 'Design',
                    'Legal' => 'Legal',
                    'Support' => 'Support',
                    'Product' => 'Product',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('department', $value)),

            SelectFilter::make('status')
                ->label('Status')
                ->setOptions([
                    '' => 'All Statuses',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->where('status', $value)),

            SelectFilter::make('department_multi')
                ->key('depts')
                ->label('Departments (multi)')
                ->multiple()
                ->searchable()
                ->setOptions([
                    'Engineering' => 'Engineering',
                    'Marketing' => 'Marketing',
                    'Sales' => 'Sales',
                    'Finance' => 'Finance',
                    'HR' => 'HR',
                    'Operations' => 'Operations',
                    'Design' => 'Design',
                    'Legal' => 'Legal',
                    'Support' => 'Support',
                    'Product' => 'Product',
                ])
                ->filter(fn (Builder $query, mixed $value) => $query->whereIn('department', $value)),

            NumberRangeFilter::make('salary')
                ->label('Salary Range')
                ->min(0.0)
                ->max(200000.0)
                ->step(1000.0)
                ->filter(function (Builder $query, mixed $value): Builder {
                    if (isset($value['min']) && $value['min'] !== '') {
                        $query->where('salary', '>=', (float) $value['min']);
                    }
                    if (isset($value['max']) && $value['max'] !== '') {
                        $query->where('salary', '<=', (float) $value['max']);
                    }

                    return $query;
                }),
        ];
    }
}
