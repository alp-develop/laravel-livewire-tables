<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;

final class DateFilter extends Filter
{
    private ?string $minDateValue = null;

    private ?string $maxDateValue = null;

    public function minDate(string $date): static
    {
        $this->minDateValue = $date;

        return $this;
    }

    public function maxDate(string $date): static
    {
        $this->maxDateValue = $date;

        return $this;
    }

    public function getMinDate(): ?string
    {
        return $this->minDateValue;
    }

    public function getMaxDate(): ?string
    {
        return $this->maxDateValue;
    }

    public function type(): string
    {
        return 'date';
    }

    public function normalizeValue(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        if ($this->minDateValue !== null && $value < $this->minDateValue) {
            return '';
        }

        if ($this->maxDateValue !== null && $value > $this->maxDateValue) {
            return '';
        }

        return $value;
    }

    public function applyFilter(Builder $query, mixed $value): Builder
    {
        return parent::applyFilter($query, $this->clampDate($value));
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        return $query->whereDate($this->fieldName, $this->clampDate($value));
    }

    private function clampDate(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        if ($this->minDateValue !== null && $value < $this->minDateValue) {
            return $this->minDateValue;
        }

        if ($this->maxDateValue !== null && $value > $this->maxDateValue) {
            return $this->maxDateValue;
        }

        return $value;
    }
}
