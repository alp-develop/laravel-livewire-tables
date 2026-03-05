<?php

declare(strict_types=1);

namespace Livewire\Tables\Core;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Core\Contracts\EngineContract;
use Livewire\Tables\Core\Contracts\FilterContract;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Contracts\StepContract;

final class Engine implements EngineContract
{
    /** @var array<int, StepContract> */
    private array $steps = [];

    /**
     * @param  array<int, ColumnContract>  $columns
     * @param  array<int, FilterContract>  $filters
     */
    public function __construct(
        private readonly array $columns = [],
        private readonly array $filters = [],
    ) {}

    public function process(Builder $query, StateContract $state): LengthAwarePaginator
    {
        foreach ($this->steps as $step) {
            $query = $step->apply($query, $state);
        }

        return $query->paginate(
            perPage: $state->perPage(),
            page: $state->page(),
        );
    }

    public function addStep(StepContract $step): static
    {
        $this->steps[] = $step;

        return $this;
    }

    /** @return array<int, ColumnContract> */
    public function columns(): array
    {
        return $this->columns;
    }

    /** @return array<int, FilterContract> */
    public function filters(): array
    {
        return $this->filters;
    }
}
