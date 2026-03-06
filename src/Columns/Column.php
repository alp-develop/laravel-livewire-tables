<?php

declare(strict_types=1);

namespace Livewire\Tables\Columns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Core\Contracts\SearchableContract;
use Livewire\Tables\Themes\ThemeManager;

abstract class Column implements ColumnContract, SearchableContract
{
    protected string $labelText = '';

    protected string $keyName = '';

    protected bool $sortable = false;

    protected bool $searchable = false;

    protected ?string $searchFieldName = null;

    protected ?Closure $searchCallback = null;

    protected bool $visible = true;

    protected ?string $widthValue = null;

    protected ?string $selectAlias = null;

    protected string $headerClassValue = '';

    protected string $cellClassValue = '';

    protected string $columnClassValue = '';

    protected ?Closure $formatCallback = null;

    protected ?Closure $renderCallback = null;

    protected ?string $viewName = null;

    protected bool $hiddenIfValue = false;

    public function __construct(
        protected readonly string $fieldName,
    ) {
        $bare = str_contains($fieldName, '.') ? substr($fieldName, strrpos($fieldName, '.') + 1) : $fieldName;
        $this->labelText = str_replace('_', ' ', ucfirst($bare));
    }

    public static function text(string $field): TextColumn
    {
        return new TextColumn($field);
    }

    public static function boolean(string $field): BooleanColumn
    {
        return new BooleanColumn($field);
    }

    public static function date(string $field): DateColumn
    {
        return new DateColumn($field);
    }

    public static function image(string $field): ImageColumn
    {
        return new ImageColumn($field);
    }

    public static function blade(string $field = '_blade'): BladeColumn
    {
        return BladeColumn::make($field);
    }

    public static function actions(string $field = '_actions'): ActionColumn
    {
        return ActionColumn::make($field);
    }

    public function label(string $label): static
    {
        $this->labelText = $label;

        return $this;
    }

    public function render(Closure $callback): static
    {
        $this->renderCallback = $callback;

        return $this;
    }

    public function key(string $key): static
    {
        $this->keyName = $key;

        return $this;
    }

    public function getKey(): string
    {
        return $this->keyName !== '' ? $this->keyName : $this->fieldName;
    }

    public function sortable(): static
    {
        $this->sortable = true;

        return $this;
    }

    public function searchable(string|Closure|null $searchUsing = null): static
    {
        $this->searchable = true;

        if (is_string($searchUsing)) {
            $this->searchFieldName = $searchUsing;
        } elseif ($searchUsing instanceof Closure) {
            $this->searchCallback = $searchUsing;
        }

        return $this;
    }

    public function getSearchField(): ?string
    {
        return $this->searchFieldName;
    }

    public function getSearchCallback(): ?Closure
    {
        return $this->searchCallback;
    }

    public function hidden(): static
    {
        $this->visible = false;

        return $this;
    }

    public function hideIf(bool $condition): static
    {
        $this->hiddenIfValue = $condition;

        return $this;
    }

    public function selectAs(string $alias): static
    {
        $this->selectAlias = $alias;

        return $this;
    }

    public function getSelectAlias(): ?string
    {
        return $this->selectAlias;
    }

    protected function resolutionKey(): string
    {
        if ($this->selectAlias !== null) {
            return $this->selectAlias;
        }

        if (str_contains($this->fieldName, '.')) {
            return str_replace('.', '_', $this->fieldName);
        }

        return $this->fieldName;
    }

    protected function resolveKeyForRow(Model $row): string
    {
        $key = $this->resolutionKey();

        if ($this->selectAlias === null && str_contains($this->fieldName, '.')) {
            $attributes = $row->getAttributes();
            if (! array_key_exists($key, $attributes)) {
                $bare = substr($this->fieldName, strrpos($this->fieldName, '.') + 1);
                if (array_key_exists($bare, $attributes)) {
                    return $bare;
                }
            }
        }

        return $key;
    }

    public function headerClass(string $class): static
    {
        $this->headerClassValue = $class;

        return $this;
    }

    public function cellClass(string $class): static
    {
        $this->cellClassValue = $class;

        return $this;
    }

    public function columnClass(string $class): static
    {
        $this->columnClassValue = $class;

        return $this;
    }

    public function getHeaderClass(): string
    {
        return $this->toImportant(trim($this->columnClassValue.' '.$this->headerClassValue));
    }

    public function getCellClass(): string
    {
        return $this->toImportant(trim($this->columnClassValue.' '.$this->cellClassValue));
    }

    private function toImportant(string $classes): string
    {
        if ($classes === '') {
            return '';
        }

        if (! app(ThemeManager::class)->resolve()->supportsImportantPrefix()) {
            return $classes;
        }

        return implode(' ', array_map(
            fn (string $class) => str_starts_with($class, '!') ? $class : '!'.$class,
            explode(' ', $classes),
        ));
    }

    public function width(string $width): static
    {
        $this->widthValue = $width;

        return $this;
    }

    public function format(string|Closure $callback): static
    {
        if ($callback instanceof Closure) {
            $this->formatCallback = $callback;
        } else {
            $format = $callback;
            $this->formatCallback = fn (mixed $value) => sprintf($format, $value);
        }

        return $this;
    }

    public function view(string $view): static
    {
        $this->viewName = $view;

        return $this;
    }

    public function type(): string
    {
        return 'text';
    }

    public function field(): string
    {
        return $this->fieldName;
    }

    public function getLabel(): string
    {
        return $this->labelText;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function isHiddenIf(): bool
    {
        return $this->hiddenIfValue;
    }

    public function getWidth(): ?string
    {
        return $this->widthValue;
    }

    public function getView(): ?string
    {
        return $this->viewName;
    }

    public function hasFormat(): bool
    {
        return $this->formatCallback !== null;
    }

    public function resolveValue(Model $row): mixed
    {
        if ($this->renderCallback !== null) {
            return ($this->renderCallback)($row);
        }

        $value = data_get($row, $this->resolveKeyForRow($row));

        if ($this->formatCallback !== null) {
            return ($this->formatCallback)($value, $row);
        }

        return $value;
    }
}
