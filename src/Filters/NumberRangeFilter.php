<?php

declare(strict_types=1);

namespace Livewire\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;

final class NumberRangeFilter extends Filter
{
    private ?float $minBound = null;

    private ?float $maxBound = null;

    private ?float $stepValue = null;

    public function min(float $min): static
    {
        $this->minBound = $min;

        return $this;
    }

    public function max(float $max): static
    {
        $this->maxBound = $max;

        return $this;
    }

    public function step(float $step): static
    {
        $this->stepValue = $step;

        return $this;
    }

    public function getMin(): ?float
    {
        return $this->minBound;
    }

    public function getMax(): ?float
    {
        return $this->maxBound;
    }

    public function getStep(): ?float
    {
        return $this->stepValue;
    }

    public function type(): string
    {
        return 'number_range';
    }

    public function normalizeValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        foreach (['min', 'max'] as $key) {
            if (! isset($value[$key]) || $value[$key] === '') {
                continue;
            }

            $num = (float) $value[$key];

            if ($this->minBound !== null && $num < $this->minBound) {
                $value[$key] = '';
            } elseif ($this->maxBound !== null && $num > $this->maxBound) {
                $value[$key] = '';
            }
        }

        return $value;
    }

    public function applyFilter(Builder $query, mixed $value): Builder
    {
        return parent::applyFilter($query, $this->clampRange($value));
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_array($value)) {
            return $query;
        }

        $value = $this->clampRange($value);
        $min = $value['min'] ?? null;
        $max = $value['max'] ?? null;

        if ($min !== null && $min !== '') {
            $query->where($this->fieldName, '>=', (float) $min);
        }

        if ($max !== null && $max !== '') {
            $query->where($this->fieldName, '<=', (float) $max);
        }

        return $query;
    }

    private function clampRange(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (isset($value['min']) && $value['min'] !== '') {
            $minNum = (float) $value['min'];
            if ($this->minBound !== null) {
                $minNum = max($minNum, $this->minBound);
            }
            if ($this->maxBound !== null) {
                $minNum = min($minNum, $this->maxBound);
            }
            $value['min'] = $minNum;
        }

        if (isset($value['max']) && $value['max'] !== '') {
            $maxNum = (float) $value['max'];
            if ($this->minBound !== null) {
                $maxNum = max($maxNum, $this->minBound);
            }
            if ($this->maxBound !== null) {
                $maxNum = min($maxNum, $this->maxBound);
            }
            $value['max'] = $maxNum;
        }

        return $value;
    }
}
