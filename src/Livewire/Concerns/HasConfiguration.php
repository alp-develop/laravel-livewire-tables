<?php

declare(strict_types=1);

namespace Livewire\Tables\Livewire\Concerns;

use Closure;
use Livewire\Tables\Themes\ThemeManager;

trait HasConfiguration
{
    /** @var array<int, int> */
    protected array $perPageOptions = [10, 25, 50, 100];

    protected int $defaultPerPage = 10;

    protected int $searchDebounce = 300;

    protected string $defaultSortDirection = 'asc';

    protected string $emptyMessage = '';

    protected string $headClassValue = '';

    protected string $bodyClassValue = '';

    protected string|Closure|null $rowClassValue = null;

    protected string $filterGroupClassValue = '';

    protected string $filterLabelClassValue = '';

    protected string $filterInputClassValue = '';

    protected string $filterBtnClassValue = '';

    protected string $filterBtnActiveClassValue = '';

    protected string $columnBtnClassValue = '';

    protected string $bulkBtnClassValue = '';

    protected string $bulkBtnActiveClassValue = '';

    /** @var array<int, string> */
    protected array $eagerLoad = [];

    protected function loadConfiguration(): void
    {
        $this->searchDebounce = (int) config('livewire-tables.search_debounce', 300);
    }

    public function configure(): void {}

    /** @param array<int, int> $options */
    protected function setPerPageOptions(array $options): static
    {
        $this->perPageOptions = $options;

        return $this;
    }

    protected function setDefaultPerPage(int $perPage): static
    {
        $this->defaultPerPage = $perPage;

        return $this;
    }

    protected function setSearchDebounce(int $milliseconds): static
    {
        $this->searchDebounce = max(0, min($milliseconds, 5000));

        return $this;
    }

    protected function setDefaultSortDirection(string $direction): static
    {
        $this->defaultSortDirection = in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc';

        return $this;
    }

    protected function setEmptyMessage(string $message): static
    {
        $this->emptyMessage = $message;

        return $this;
    }

    protected function setHeadClass(string $class): static
    {
        $this->headClassValue = $class;

        return $this;
    }

    protected function setBodyClass(string $class): static
    {
        $this->bodyClassValue = $class;

        return $this;
    }

    protected function setRowClass(string|Closure $class): static
    {
        $this->rowClassValue = $class;

        return $this;
    }

    public function getHeadClass(): string
    {
        return $this->headClassValue;
    }

    public function getBodyClass(): string
    {
        return $this->bodyClassValue;
    }

    public function resolveRowClass(mixed $row): string
    {
        if ($this->rowClassValue === null) {
            return '';
        }

        if ($this->rowClassValue instanceof Closure) {
            return ($this->rowClassValue)($row);
        }

        return $this->rowClassValue;
    }

    protected function setFilterGroupClass(string $class): static
    {
        $this->filterGroupClassValue = $class;

        return $this;
    }

    protected function setFilterLabelClass(string $class): static
    {
        $this->filterLabelClassValue = $class;

        return $this;
    }

    protected function setFilterInputClass(string $class): static
    {
        $this->filterInputClassValue = $class;

        return $this;
    }

    public function getFilterGroupClass(): string
    {
        return $this->filterGroupClassValue;
    }

    public function getFilterLabelClass(): string
    {
        return $this->filterLabelClassValue;
    }

    public function getFilterInputClass(): string
    {
        return $this->filterInputClassValue;
    }

    protected function setFilterBtnClass(string $class): static
    {
        $this->filterBtnClassValue = $class;

        return $this;
    }

    public function getFilterBtnClass(): string
    {
        return $this->filterBtnClassValue;
    }

    protected function setFilterBtnActiveClass(string $class): static
    {
        $this->filterBtnActiveClassValue = $class;

        return $this;
    }

    public function getFilterBtnActiveClass(): string
    {
        return $this->filterBtnActiveClassValue;
    }

    protected function setColumnBtnClass(string $class): static
    {
        $this->columnBtnClassValue = $class;

        return $this;
    }

    public function getColumnBtnClass(): string
    {
        return $this->columnBtnClassValue;
    }

    protected function setBulkBtnClass(string $class): static
    {
        $this->bulkBtnClassValue = $class;

        return $this;
    }

    public function getBulkBtnClass(): string
    {
        return $this->bulkBtnClassValue;
    }

    protected function setBulkBtnActiveClass(string $class): static
    {
        $this->bulkBtnActiveClassValue = $class;

        return $this;
    }

    public function getBulkBtnActiveClass(): string
    {
        return $this->bulkBtnActiveClassValue;
    }

    /**
     * @param array<int, string> $relations
     */
    protected function setEagerLoad(array $relations): static
    {
        $this->eagerLoad = $relations;

        return $this;
    }

    /** @return array<int, string> */
    public function getEagerLoad(): array
    {
        return $this->eagerLoad;
    }

    /** @return array<int, int> */
    public function getPerPageOptions(): array
    {
        return $this->perPageOptions;
    }

    public function getSearchDebounce(): int
    {
        return $this->searchDebounce;
    }

    public function getEmptyMessage(): string
    {
        if ($this->emptyMessage !== '') {
            return $this->emptyMessage;
        }

        return __('livewire-tables::messages.no_results');
    }

    public function theme(): string
    {
        return $this->tableTheme !== '' ? $this->tableTheme : app(ThemeManager::class)->active();
    }

    public function isBootstrap(): bool
    {
        return str_starts_with($this->theme(), 'bootstrap');
    }

    public function isBootstrap5(): bool
    {
        return in_array($this->theme(), ['bootstrap5', 'bootstrap-5', 'bootstrap'], true);
    }

    public function isBootstrap4(): bool
    {
        return in_array($this->theme(), ['bootstrap4', 'bootstrap-4'], true);
    }

    public function isTailwind(): bool
    {
        return $this->theme() === 'tailwind';
    }
}
