<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Core\Contracts\FilterContract;
use Livewire\Tables\Core\Engine;
use Livewire\Tables\Core\Pipeline\FilterStep;
use Livewire\Tables\Core\Pipeline\SearchStep;
use Livewire\Tables\Core\Pipeline\SortStep;
use Livewire\Tables\Core\State;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Livewire\Concerns\HasBulkActions;
use Livewire\Tables\Livewire\Concerns\HasColumns;
use Livewire\Tables\Livewire\Concerns\HasConfiguration;
use Livewire\Tables\Livewire\Concerns\HasEvents;
use Livewire\Tables\Livewire\Concerns\HasExport;
use Livewire\Tables\Livewire\Concerns\HasFilters;
use Livewire\Tables\Livewire\Concerns\HasListeners;
use Livewire\Tables\Livewire\Concerns\HasPagination;
use Livewire\Tables\Livewire\Concerns\HasPerPage;
use Livewire\Tables\Livewire\Concerns\HasSearch;
use Livewire\Tables\Livewire\Concerns\HasSorting;
use Livewire\Tables\Livewire\Concerns\HasStateCache;
use Livewire\Tables\Livewire\Concerns\HasToolbarSlots;
use Livewire\Tables\Themes\ThemeManager;

abstract class DataTableComponent extends Component
{
    use HasBulkActions;
    use HasColumns;
    use HasConfiguration;
    use HasEvents;
    use HasExport;
    use HasFilters;
    use HasListeners;
    use HasPagination;
    use HasPerPage;
    use HasSearch;
    use HasSorting;
    use HasStateCache;
    use HasToolbarSlots;

    #[Locked]
    public string $tableTheme = '';

    public bool $darkMode = false;

    abstract public function query(): Builder;

    /** @return array<int, FilterContract> */
    public function filters(): array
    {
        return [];
    }

    public function build(): void {}

    public function executeBulkAction(string $action): mixed
    {
        if (! $this->hasBulkActions() || ! array_key_exists($action, $this->bulkActions())) {
            return null;
        }

        $result = $this->{$action}();
        $this->deselectAll();

        return $result;
    }

    public function getSelectedIds(): array
    {
        if (! $this->selectAllPages) {
            return $this->selectedIds;
        }

        $columns = $this->resolveColumns();
        $filters = $this->filters();
        $state = new State(search: $this->search, filters: $this->tableFilters);
        $query = $this->query();
        $query = (new SearchStep($columns))->apply($query, $state);
        $query = (new FilterStep($filters))->apply($query, $state);

        $keyName = $query->getModel()->getKeyName();
        $allIds = $query->pluck($keyName)->map(fn ($id) => (string) $id)->toArray();

        return array_values(array_diff($allIds, $this->excludedIds));
    }

    public function boot(): void
    {
        if ($this->tableTheme === '') {
            $this->tableTheme = app(ThemeManager::class)->active();
        } else {
            app(ThemeManager::class)->use($this->tableTheme);
        }

        if (config('livewire-tables.dark_mode.enabled', false)) {
            $selector = config('livewire-tables.dark_mode.selector', 'lt-dark');
            $this->darkMode = (bool) session($selector, false);
        }

        $this->loadConfiguration();
        $this->configure();
    }

    public function mount(string $tableTheme = ''): void
    {
        if ($tableTheme !== '' && $tableTheme !== $this->tableTheme) {
            $this->tableTheme = $tableTheme;
            app(ThemeManager::class)->use($tableTheme);
            $this->loadConfiguration();
            $this->configure();
        }

        $this->perPage = $this->defaultPerPage;
        $this->loadStateFromCache();

        foreach ($this->filters() as $filter) {
            $key = $filter->getKey();
            if ($filter->hasInitialValue() && ! array_key_exists($key, $this->tableFilters)) {
                $normalized = $filter->normalizeValue($filter->getInitialValue());
                $this->tableFilters[$key] = $normalized;
            }
        }

        $this->build();

        if ($this->hasActiveFilters()) {
            $this->dispatchFiltersChanged();
        }
    }

    public function updatedTableFilters(mixed $value, ?string $key): void
    {
        $this->deselectAll();
        $this->resetPage();

        if ($key === null) {
            return;
        }

        $filterKey = str_contains($key, '.') ? explode('.', $key)[0] : $key;
        $activeFilter = $this->getFilterByKey($filterKey);

        if ($activeFilter !== null && isset($this->tableFilters[$filterKey])) {
            $this->tableFilters[$filterKey] = $activeFilter->normalizeValue($this->tableFilters[$filterKey]);
        }

        foreach ($this->filters() as $filter) {
            if (
                $filter instanceof SelectFilter
                && $filter->hasDependency()
                && $filter->getParent() === $filterKey
            ) {
                $this->tableFilters[$filter->getKey()] = '';
            }
        }

        $this->dispatchFiltersChanged();
    }

    public function resolveParentValue(string $parentKey): mixed
    {
        foreach ($this->filters() as $filter) {
            if ($filter->getKey() === $parentKey) {
                return $this->tableFilters[$filter->getKey()] ?? '';
            }
        }

        return $this->tableFilters[$parentKey] ?? '';
    }

    public function getFilterByKey(string $key): ?FilterContract
    {
        foreach ($this->filters() as $filter) {
            if ($filter->getKey() === $key) {
                return $filter;
            }
        }

        return null;
    }

    /** @return array<string, mixed> */
    public function getAppliedFilters(): array
    {
        $applied = [];

        foreach ($this->filters() as $filter) {
            $value = $this->tableFilters[$filter->getKey()] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value)) {
                $parts = array_filter($value, fn ($v) => $v !== null && $v !== '');
                if (count($parts) === 0) {
                    continue;
                }
            }

            $applied[$filter->getKey()] = $value;
        }

        return $applied;
    }

    private function resolveParentFieldFromKey(string $parentKey): string
    {
        foreach ($this->filters() as $filter) {
            if ($filter->getKey() === $parentKey) {
                return $filter->field();
            }
        }

        return $parentKey;
    }

    public function render(): View
    {
        $this->configure();

        $columns = $this->resolveColumns();
        $filters = $this->filters();

        $engine = new Engine(
            columns: $columns,
            filters: $filters,
        );

        $engine->addStep(new SearchStep($columns))
            ->addStep(new FilterStep($filters))
            ->addStep(new SortStep($columns));

        $state = new State(
            search: $this->search,
            sortFields: $this->sortFields,
            filters: $this->tableFilters,
            perPage: $this->perPage,
            page: $this->getPage(),
        );

        $query = $this->query();
        $this->onQuerying($query);
        $this->fireTableEvent('querying');

        $rows = $engine->process($query, $state);

        $this->onQueried($rows);
        $this->fireTableEvent('queried');

        $themeManager = app(ThemeManager::class);
        $themeManager->use($this->tableTheme ?: $themeManager->active());
        $theme = $themeManager->resolve();
        $themeClasses = $theme->classes();

        if ($this->hasBulkActions() && $rows instanceof LengthAwarePaginator) {
            $keyName = $rows->first()?->getKeyName() ?? 'id';
            $this->pageIds = array_map('strval', $rows->pluck($keyName)->toArray());
        }

        $viewData = [
            'allColumns' => $this->getAllColumns(),
            'columns' => $this->getVisibleColumns(),
            'rows' => $rows,
            'filters' => $filters,
            'activeFilters' => $this->buildActiveFilterChips($filters),
            'sortChips' => $this->buildSortChips($columns),
            'classes' => $themeClasses,
            'bulkActions' => $this->bulkActions(),
            'totalRows' => $rows->total(),
            'headClass' => $this->resolveButtonClass($this->getHeadClass(), $themeClasses['thead']),
            'bodyClass' => trim($themeClasses['tbody'].($this->getBodyClass() !== '' ? ' '.$this->getBodyClass() : '')),
            'filterBtnClass' => $this->resolveFilterBtnClass($themeClasses),
            'filterBtnActiveClass' => $this->resolveButtonClass($this->getFilterBtnActiveClass(), $themeClasses['filter-btn-active']),
            'columnBtnClass' => $this->resolveButtonClass($this->getColumnBtnClass(), $themeClasses['column-btn']),
            'bulkBtnClass' => $this->resolveButtonClass($this->getBulkBtnClass(), $themeClasses['bulk-btn']),
            'bulkBtnActiveClass' => $this->resolveButtonClass($this->getBulkBtnActiveClass(), $themeClasses['bulk-btn-active']),
            'filterLabelClass' => $this->resolveButtonClass($this->getFilterLabelClass(), $themeClasses['filter-label']),
            'filterGroupClass' => $this->resolveButtonClass($this->getFilterGroupClass(), $themeClasses['filter-group']),
            'filterInputClass' => $themeClasses['filter-input'].($this->getFilterInputClass() !== '' ? ' '.$this->getFilterInputClass() : ''),
            'filterSelectClass' => $themeClasses['filter-select'].($this->getFilterInputClass() !== '' ? ' '.$this->getFilterInputClass() : ''),
            'paginationView' => $this->paginationView(),
        ];

        $viewData = $this->onRendering($viewData);
        $this->fireTableEvent('rendering');

        /** @var view-string $viewName */
        $viewName = 'livewire-tables::components.table';
        $view = view($viewName, $viewData);

        $this->onRendered();
        $this->fireTableEvent('rendered');

        return $view;
    }

    /** @param array<int, FilterContract> $filters */
    private function buildActiveFilterChips(array $filters): array
    {
        $chips = [];

        foreach ($filters as $filter) {
            $value = $this->tableFilters[$filter->getKey()] ?? null;

            if (is_array($value)) {
                $parts = array_filter($value, fn ($v) => $v !== null && $v !== '');
                if (count($parts) === 0) {
                    continue;
                }
                $displayValue = implode(' - ', array_values($parts));
            } else {
                if ($value === null || $value === '') {
                    continue;
                }
                $displayValue = match ($filter->type()) {
                    'select' => ($filter instanceof SelectFilter && $filter->hasDependency())
                        ? ($filter->resolveOptions($this->resolveParentValue($filter->getParent() ?? ''))[$value] ?? (string) $value)
                        : ($filter->options()[$value] ?? (string) $value),
                    'boolean' => ((int) $value === 1)
                        ? __('livewire-tables::messages.yes')
                        : __('livewire-tables::messages.no'),
                    default => (string) $value,
                };
            }

            $chips[] = [
                'key' => $filter->getKey(),
                'label' => $filter->getLabel(),
                'value' => $displayValue,
            ];
        }

        return $chips;
    }

    /** @param array<int, ColumnContract> $columns */
    private function buildSortChips(array $columns): array
    {
        $chips = [];

        foreach ($this->sortFields as $field => $direction) {
            foreach ($columns as $col) {
                if ($col->field() === $field) {
                    $chips[] = [
                        'field' => $field,
                        'label' => $col->getLabel(),
                        'direction' => $direction,
                        'order' => $this->getSortOrder($field),
                    ];
                    break;
                }
            }
        }

        return $chips;
    }

    /** @param array<string, string> $themeClasses */
    private function resolveFilterBtnClass(array $themeClasses): string
    {
        return $this->hasActiveFilters()
            ? $this->resolveButtonClass($this->getFilterBtnActiveClass(), $themeClasses['filter-btn-active'])
            : $this->resolveButtonClass($this->getFilterBtnClass(), $themeClasses['filter-btn']);
    }

    private function resolveButtonClass(string $custom, string $default): string
    {
        return $custom !== '' ? $custom : $default;
    }
}
