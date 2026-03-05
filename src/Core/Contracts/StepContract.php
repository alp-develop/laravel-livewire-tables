<?php

declare(strict_types=1);

namespace Livewire\Tables\Core\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface StepContract
{
    public function apply(Builder $query, StateContract $state): Builder;
}
