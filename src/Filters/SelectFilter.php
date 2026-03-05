<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

final class SelectFilter extends Filter
{
    /** @var array<string, string> */
    private array $selectOptions = [];

    private ?string $parentField = null;

    private ?Closure $parentFilterCallback = null;

    private bool $multipleValue = false;

    private bool $searchableValue = false;

    /** @param array<string, string> $options */
    public function setOptions(array $options): static
    {
        $this->selectOptions = $options;

        return $this;
    }

    /** @return array<string, string> */
    public function options(): array
    {
        return $this->selectOptions;
    }

    public function parent(string $field): static
    {
        $this->parentField = $field;

        return $this;
    }

    public function parentFilter(Closure $callback): static
    {
        $this->parentFilterCallback = $callback;

        return $this;
    }

    public function hasDependency(): bool
    {
        return $this->parentField !== null;
    }

    public function getParent(): ?string
    {
        return $this->parentField;
    }

    /** @return array<string, string> */
    public function resolveOptions(mixed $value): array
    {
        if ($this->parentFilterCallback === null || $value === '' || $value === null) {
            return [];
        }

        return ($this->parentFilterCallback)($value);
    }

    public function multiple(): static
    {
        $this->multipleValue = true;

        return $this;
    }

    public function searchable(): static
    {
        $this->searchableValue = true;

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multipleValue;
    }

    public function isSearchable(): bool
    {
        return $this->searchableValue;
    }

    public function type(): string
    {
        return $this->multipleValue ? 'multi_select' : 'select';
    }

    public function normalizeValue(mixed $value): mixed
    {
        if (! $this->multipleValue || ! is_array($value)) {
            return $value;
        }

        $valid = array_keys($this->selectOptions);

        return array_values(array_filter($value, fn ($v) => in_array($v, $valid, true)));
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($this->multipleValue) {
            if (! is_array($value) || count($value) === 0) {
                return $query;
            }

            return $query->whereIn($this->fieldName, $value);
        }

        return $query->where($this->fieldName, $value);
    }
}
