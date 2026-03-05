<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;

final class NumberFilter extends Filter
{
    private ?float $minValue = null;

    private ?float $maxValue = null;

    private ?float $stepValue = null;

    public function min(float $min): static
    {
        $this->minValue = $min;

        return $this;
    }

    public function max(float $max): static
    {
        $this->maxValue = $max;

        return $this;
    }

    public function step(float $step): static
    {
        $this->stepValue = $step;

        return $this;
    }

    public function getMin(): ?float
    {
        return $this->minValue;
    }

    public function getMax(): ?float
    {
        return $this->maxValue;
    }

    public function getStep(): ?float
    {
        return $this->stepValue;
    }

    public function type(): string
    {
        return 'number';
    }

    public function normalizeValue(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $num = (float) $value;

        if ($this->minValue !== null && $num < $this->minValue) {
            return '';
        }

        if ($this->maxValue !== null && $num > $this->maxValue) {
            return '';
        }

        return $value;
    }

    public function applyFilter(Builder $query, mixed $value): Builder
    {
        return parent::applyFilter($query, $this->clampValue($value));
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        return $query->where($this->fieldName, $this->clampValue($value));
    }

    private function clampValue(mixed $value): float
    {
        $num = (float) $value;

        if ($this->minValue !== null) {
            $num = max($num, $this->minValue);
        }

        if ($this->maxValue !== null) {
            $num = min($num, $this->maxValue);
        }

        return $num;
    }
}
