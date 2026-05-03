<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface EngineContract
{
    public function process(Builder $query, StateContract $state): LengthAwarePaginator;

    public function applySteps(Builder $query, StateContract $state): Builder;

    public function addStep(StepContract $step): static;
}
