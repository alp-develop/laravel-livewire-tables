<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Contracts\FilterContract;

abstract class Filter implements FilterContract
{
    protected string $labelText = '';

    protected string $keyName = '';

    protected string $placeholderText = '';

    protected mixed $defaultVal = null;

    protected mixed $initialVal = null;

    protected bool $hasInitialVal = false;

    protected ?Closure $queryCallback = null;

    protected string $groupClassValue = '';

    protected string $labelClassValue = '';

    protected string $inputClassValue = '';

    public function __construct(
        protected readonly string $fieldName,
    ) {
        $bare = str_contains($fieldName, '.') ? substr($fieldName, strrpos($fieldName, '.') + 1) : $fieldName;
        $this->labelText = str_replace('_', ' ', ucfirst($bare));
    }

    public static function make(string $field): static
    {
        return new static($field); // @phpstan-ignore new.static
    }

    public function label(string $label): static
    {
        $this->labelText = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->labelText;
    }

    public function key(string $key): static
    {
        $this->keyName = $key;

        return $this;
    }

    public function getKey(): string
    {
        if ($this->keyName !== '') {
            return $this->keyName;
        }

        return str_replace('.', '_', $this->fieldName);
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholderText = $placeholder;

        return $this;
    }

    public function default(mixed $value): static
    {
        $this->defaultVal = $value;

        return $this;
    }

    public function initialValue(mixed $value): static
    {
        $this->initialVal = $value;
        $this->hasInitialVal = true;

        return $this;
    }

    public function getInitialValue(): mixed
    {
        return $this->initialVal;
    }

    public function hasInitialValue(): bool
    {
        return $this->hasInitialVal;
    }

    public function filter(Closure $cb): static
    {
        $this->queryCallback = $cb;

        return $this;
    }

    public function filterClass(string $class): static
    {
        return $this->groupClass($class);
    }

    public function groupClass(string $class): static
    {
        $this->groupClassValue = $class;

        return $this;
    }

    public function labelClass(string $class): static
    {
        $this->labelClassValue = $class;

        return $this;
    }

    public function inputClass(string $class): static
    {
        $this->inputClassValue = $class;

        return $this;
    }

    public function getGroupClass(): string
    {
        return $this->groupClassValue;
    }

    public function getLabelClass(): string
    {
        return $this->labelClassValue;
    }

    public function getInputClass(): string
    {
        return $this->inputClassValue;
    }

    public function field(): string
    {
        return $this->fieldName;
    }

    public function defaultValue(): mixed
    {
        return $this->defaultVal;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholderText;
    }

    public function normalizeValue(mixed $value): mixed
    {
        return $value;
    }

    public function hasFilter(): bool
    {
        return $this->queryCallback !== null;
    }

    public function applyFilter(Builder $query, mixed $value): Builder
    {
        if ($this->queryCallback === null) {
            return $query;
        }

        return ($this->queryCallback)($query, $value);
    }

    /** @return array<string, string> */
    public function options(): array
    {
        return [];
    }

    abstract public function type(): string;

    abstract public function apply(Builder $query, mixed $value): Builder;
}
