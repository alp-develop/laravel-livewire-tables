<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Pipeline;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Contracts\StepContract;

final class FilterStep implements StepContract
{
    public function __construct(
        private readonly array $filters = [],
    ) {}

    public function apply(Builder $query, StateContract $state): Builder
    {
        $activeFilters = $state->filters();

        if (count($activeFilters) === 0) {
            return $query;
        }

        $filterMap = [];
        foreach ($this->filters as $filter) {
            $filterMap[$filter->getKey()] = $filter;
        }

        foreach ($activeFilters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value) && count(array_filter($value, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            if (! isset($filterMap[$field])) {
                continue;
            }

            $filter = $filterMap[$field];
            $query = $filter->hasFilter()
                ? $filter->applyFilter($query, $value)
                : $filter->apply($query, $value);
        }

        return $query;
    }
}
