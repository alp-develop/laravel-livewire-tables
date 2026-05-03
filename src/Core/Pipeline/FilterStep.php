<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Pipeline;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Contracts\FilterContract;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Contracts\StepContract;

final class FilterStep implements StepContract
{
    /** @var array<string, FilterContract> */
    private readonly array $filterMap;

    public function __construct(
        private readonly array $filters = [],
    ) {
        $map = [];
        foreach ($this->filters as $filter) {
            $map[$filter->getKey()] = $filter;
        }
        $this->filterMap = $map;
    }

    public function apply(Builder $query, StateContract $state): Builder
    {
        $activeFilters = $state->filters();

        if (count($activeFilters) === 0) {
            return $query;
        }

        foreach ($activeFilters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value) && count(array_filter($value, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            if (! isset($this->filterMap[$field])) {
                continue;
            }

            $filter = $this->filterMap[$field];
            $query = $filter->run($query, $value);
        }

        return $query;
    }
}
