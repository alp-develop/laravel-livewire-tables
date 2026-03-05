<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Contracts\StepContract;
use Livewire\Tables\Core\Engine;

test('engine can add steps', function (): void {
    $engine = new Engine;

    $step = new class implements StepContract
    {
        public function apply(Builder $query, StateContract $state): Builder
        {
            return $query;
        }
    };

    $result = $engine->addStep($step);

    expect($result)->toBeInstanceOf(Engine::class);
});

test('engine stores columns and filters', function (): void {
    $engine = new Engine(columns: [], filters: []);

    expect($engine->columns())->toBe([])
        ->and($engine->filters())->toBe([]);
});
