<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Tables\Core\Contracts\StateContract;
use Livewire\Tables\Core\Contracts\StepContract;
use Livewire\Tables\Core\Engine;
use Livewire\Tables\Core\State;

beforeEach(function (): void {
    Schema::connection('testing')->create('engine_items', function ($table): void {
        $table->id();
        $table->string('name')->nullable();
    });
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('engine_items');
});

function makeEngineModel(): Model
{
    return new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'engine_items';

        protected $guarded = [];
    };
}

function makeEngineState(): StateContract
{
    return new State(
        search: '',
        sortFields: [],
        filters: [],
        perPage: 10,
        page: 1,
    );
}

test('applySteps applies each step to the query', function (): void {
    $called = [];

    $stepA = new class ($called, 'A') implements StepContract
    {
        public function __construct(private array &$called, private string $name) {}

        public function apply(Builder $query, StateContract $state): Builder
        {
            $this->called[] = $this->name;

            return $query;
        }
    };

    $stepB = new class ($called, 'B') implements StepContract
    {
        public function __construct(private array &$called, private string $name) {}

        public function apply(Builder $query, StateContract $state): Builder
        {
            $this->called[] = $this->name;

            return $query;
        }
    };

    $engine = (new Engine)->addStep($stepA)->addStep($stepB);
    $query = makeEngineModel()->newQuery();
    $state = makeEngineState();

    $engine->applySteps($query, $state);

    expect($called)->toBe(['A', 'B']);
});

test('applySteps returns modified query', function (): void {
    $step = new class implements StepContract
    {
        public function apply(Builder $query, StateContract $state): Builder
        {
            return $query->where('name', 'test');
        }
    };

    $engine = (new Engine)->addStep($step);
    $query = makeEngineModel()->newQuery();
    $state = makeEngineState();

    $result = $engine->applySteps($query, $state);

    expect($result->toSql())->toContain('where');
});

test('applySteps with no steps returns original query unchanged', function (): void {
    $engine = new Engine;
    $query = makeEngineModel()->newQuery();
    $state = makeEngineState();

    $result = $engine->applySteps($query, $state);

    expect($result->toSql())->toBe('select * from "engine_items"');
});

test('process calls applySteps before paginating', function (): void {
    $applied = false;

    $step = new class ($applied) implements StepContract
    {
        public function __construct(private bool &$applied) {}

        public function apply(Builder $query, StateContract $state): Builder
        {
            $this->applied = true;

            return $query;
        }
    };

    $engine = (new Engine)->addStep($step);
    $query = makeEngineModel()->newQuery();
    $state = makeEngineState();

    $engine->process($query, $state);

    expect($applied)->toBeTrue();
});

test('Engine can be extended via subclass to override addStep behavior', function (): void {
    $steps = [];

    $customEngine = new class ($steps) extends Engine
    {
        /** @var array<int, StepContract> */
        private array $recordedSteps = [];

        public function __construct(array &$steps)
        {
            parent::__construct();
            $this->recordedSteps = &$steps;
        }

        public function addStep(StepContract $step): static
        {
            $this->recordedSteps[] = $step::class;

            return parent::addStep($step);
        }
    };

    $step = new class implements StepContract
    {
        public function apply(Builder $query, StateContract $state): Builder
        {
            return $query;
        }
    };

    $customEngine->addStep($step);

    expect($steps)->toHaveCount(1);
});
