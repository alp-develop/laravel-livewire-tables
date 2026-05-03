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
    private bool $configureRan = false;

    protected ?Engine $cachedEngine = null;

    private ?ThemeManager $cachedThemeManager = null;

    /** @var array<string, FilterContract>|null */
    private ?array $cachedFilterMap = null;

    private function getThemeManager(): ThemeManager
    {
        if ($this->cachedThemeManager === null) {
            $this->cachedThemeManager = app(ThemeManager::class);
        }

        return $this->cachedThemeManager;
    }

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

    /**
     * Override to specify explicit SELECT columns and avoid SELECT *.
     * Return an array of column expressions (e.g. ['id', 'name', 'email']).
     * An empty array (default) keeps the original SELECT from query().
     *
     * @return array<int, string>
     */
    public function selectColumns(): array
    {
        return [];
    }

    /** @return array<int, FilterContract> */
    public function filters(): array
    {
        return [];
    }

    /**
     * Called once per mount (initial page load only).
     * Use for one-time initialization that should not repeat on subsequent Livewire requests.
     */
    public function build(): void {}

    /**
     * Called once per Livewire request lifecycle (inside boot()).
     * Use to set component configuration (debounce, perPage, columns, etc.).
     * Runs before render() on every request — keep it lightweight.
     */
    protected function configure(): void {}

    public function executeBulkAction(string $action): mixed
    {
        if (! $this->hasBulkActions() || ! array_key_exists($action, $this->bulkActions())) {
            return null;
        }

        $result = $this->{$action}();
        $this->deselectAll();

        return $result;
    }

    protected function getEngine(): Engine
    {
        if ($this->cachedEngine === null) {
            $columns = $this->resolveColumns();
            $filters = $this->resolveFilters();
            $this->cachedEngine = (new Engine(columns: $columns, filters: $filters))
                ->addStep(new SearchStep($columns))
                ->addStep(new FilterStep($filters))
                ->addStep(new SortStep($columns));
        }

        return $this->cachedEngine;
    }

    public function getSelectedIds(): array
    {
        $state = new State(search: $this->search, filters: $this->tableFilters);
        $query = $this->getEngine()->applySteps($this->query(), $state);

        $keyName = $query->getModel()->getKeyName();
        $allIds = array_map('strval', $query->pluck($keyName)->all());

        if ($this->selectAllPages) {
            return array_values(array_diff($allIds, $this->excludedIds));
        }

        return array_values(array_intersect($this->selectedIds, $allIds));
    }

    public function boot(): void
    {
        $themeManager = $this->getThemeManager();
        if ($this->tableTheme === '') {
            $this->tableTheme = $themeManager->active();
        } else {
            $themeManager->use($this->tableTheme);
        }

        if (config('livewire-tables.dark_mode.enabled', false)) {
            $selector = config('livewire-tables.dark_mode.selector', 'lt-dark');
            $cookieValue = request()->cookie($selector);
            $this->darkMode = $cookieValue !== null
                ? filter_var($cookieValue, FILTER_VALIDATE_BOOLEAN)
                : (bool) session($selector, false);
        }

        $this->loadConfiguration();
        $this->configureRan = false;
        $this->configure();
        $this->configureRan = true;
    }

    public function mount(string $tableTheme = ''): void
    {
        if ($tableTheme !== '' && $tableTheme !== $this->tableTheme) {
            $this->tableTheme = $tableTheme;
            $this->getThemeManager()->use($tableTheme);
            $this->loadConfiguration();
            $this->configure();
        }

        $this->perPage = $this->defaultPerPage;
        $this->loadStateFromCache();

        foreach ($this->resolveFilters() as $filter) {
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

        foreach ($this->resolveFilters() as $filter) {
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
        $map = $this->getFilterMap();
        if (isset($map[$parentKey])) {
            return $this->tableFilters[$parentKey] ?? '';
        }

        return $this->tableFilters[$parentKey] ?? '';
    }

    public function getFilterByKey(string $key): ?FilterContract
    {
        return $this->getFilterMap()[$key] ?? null;
    }

    /** @return array<string, FilterContract> */
    private function getFilterMap(): array
    {
        if ($this->cachedFilterMap === null) {
            $this->cachedFilterMap = [];
            foreach ($this->resolveFilters() as $filter) {
                $this->cachedFilterMap[$filter->getKey()] = $filter;
            }
        }

        return $this->cachedFilterMap;
    }

    /** @return array<string, mixed> */
    public function getAppliedFilters(): array
    {
        $applied = [];

        foreach ($this->resolveFilters() as $filter) {
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
        foreach ($this->resolveFilters() as $filter) {
            if ($filter->getKey() === $parentKey) {
                return $filter->field();
            }
        }

        return $parentKey;
    }

    public function render(): View
    {
        if (! $this->configureRan) {
            $this->configure();
        }

        $columns = $this->resolveColumns();
        $filters = $this->resolveFilters();
        $engine = $this->getEngine();

        $state = new State(
            search: $this->search,
            sortFields: $this->sortFields,
            filters: $this->tableFilters,
            perPage: in_array($this->perPage, $this->perPageOptions, true) ? $this->perPage : $this->defaultPerPage,
            page: $this->getPage(),
        );

        $query = $this->query();
        $selectCols = $this->selectColumns();
        if ($selectCols !== []) {
            $query->select($selectCols);
        }
        $eagerRelations = $this->getEagerLoad();
        if ($eagerRelations !== []) {
            $query->with($eagerRelations);
        }
        $this->onQuerying($query);
        $this->fireTableEvent('querying');

        $rows = $engine->process($query, $state);

        $this->onQueried($rows);
        $this->fireTableEvent('queried');

        $themeManager = $this->getThemeManager();
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
        $parentValueMap = [];

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
                        ? (function () use ($filter, $value, &$parentValueMap): string {
                            $parentKey = $filter->getParent() ?? '';
                            $parentValueMap[$parentKey] ??= $this->resolveParentValue($parentKey);

                            return $filter->resolveOptions($parentValueMap[$parentKey])[$value] ?? (string) $value;
                        })()
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

        $columnByField = [];
        foreach ($columns as $col) {
            $columnByField[$col->field()] = $col;
        }

        foreach ($this->sortFields as $field => $direction) {
            if (! isset($columnByField[$field])) {
                continue;
            }

            $chips[] = [
                'field' => $field,
                'label' => $columnByField[$field]->getLabel(),
                'direction' => $direction,
                'order' => $this->getSortOrder($field),
            ];
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
