<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Livewire\DataTableComponent;
use Livewire\Tables\Themes\ThemeManager;

// --- Inline Model -----------------------------------------------------------
class BootstrapTestItem extends Model
{
    protected $connection = 'testing';

    protected $table = 'bs_test_items';

    protected $guarded = [];

    public $timestamps = false;
}

// --- Inline Component -------------------------------------------------------
class BootstrapCustomClassTable extends DataTableComponent
{
    public string $tableKey = 'bs-custom-test';

    public function configure(): void
    {
        $this->setDefaultPerPage(10);

        if ($this->isBootstrap()) {
            $this->setHeadClass('lt-thead-tinted');
            $this->setFilterLabelClass('form-label small mb-1 fw-semibold lt-text-700');
            $this->setFilterBtnClass('btn btn-sm rounded-3 lt-btn-subtle d-inline-flex align-items-center gap-1 fw-medium');
            $this->setFilterBtnActiveClass('btn btn-sm rounded-3 lt-btn-active-soft d-inline-flex align-items-center gap-1 fw-medium');
            $this->setColumnBtnClass('btn btn-sm rounded-3 lt-btn-subtle d-inline-flex align-items-center gap-1 fw-medium');
            $this->setBulkBtnClass('btn btn-sm rounded-3 lt-btn-subtle d-inline-flex align-items-center gap-1 fw-medium opacity-50 pe-none');
            $this->setBulkBtnActiveClass('btn btn-sm rounded-3 lt-btn-active-soft d-inline-flex align-items-center gap-1 fw-medium');
        } else {
            $this->setHeadClass('bg-sky-50 text-sky-800');
            $this->setFilterLabelClass('font-semibold text-sky-700');
        }
    }

    public function query(): Builder
    {
        return BootstrapTestItem::query();
    }

    public function columns(): array
    {
        return [
            TextColumn::make('name')->label('Name')->sortable()->searchable(),
            TextColumn::make('value')->label('Value')->sortable(),
        ];
    }
}

// --- Tests ------------------------------------------------------------------
beforeEach(function () {
    Schema::connection('testing')->create('bs_test_items', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('value')->nullable();
    });

    BootstrapTestItem::insert([
        ['name' => 'Alpha', 'value' => '100'],
        ['name' => 'Beta', 'value' => '200'],
    ]);
});

afterEach(function () {
    Schema::connection('testing')->dropIfExists('bs_test_items');
});

it('applies custom head class when theme is bootstrap via config', function () {
    // Set config to bootstrap
    config()->set('livewire-tables.theme', 'bootstrap');
    app()->forgetInstance(ThemeManager::class);
    app()->singleton(ThemeManager::class, fn () => new ThemeManager(active: 'bootstrap'));

    $component = Livewire::test(BootstrapCustomClassTable::class);

    // Check isBootstrap returns true
    expect($component->instance()->isBootstrap())->toBeTrue();
    expect($component->instance()->theme())->toBe('bootstrap');
    expect($component->instance()->tableTheme)->toBe('bootstrap');

    // Check custom class values are set
    expect($component->instance()->getHeadClass())->toBe('lt-thead-tinted');
    expect($component->instance()->getFilterLabelClass())->toBe('form-label small mb-1 fw-semibold lt-text-700');
    expect($component->instance()->getFilterBtnClass())->toBe('btn btn-sm rounded-3 lt-btn-subtle d-inline-flex align-items-center gap-1 fw-medium');
    expect($component->instance()->getColumnBtnClass())->toBe('btn btn-sm rounded-3 lt-btn-subtle d-inline-flex align-items-center gap-1 fw-medium');

    // Check HTML output contains the custom class
    $component->assertSeeHtml('lt-thead-tinted');
    $component->assertDontSeeHtml('class="bg-light lt-thead-tinted"');
});

it('applies custom head class when theme is bootstrap via prop', function () {
    // Config is tailwind, but we pass bootstrap as prop
    config()->set('livewire-tables.theme', 'tailwind');
    app()->forgetInstance(ThemeManager::class);
    app()->singleton(ThemeManager::class, fn () => new ThemeManager(active: 'tailwind'));

    $component = Livewire::test(BootstrapCustomClassTable::class, ['tableTheme' => 'bootstrap']);

    expect($component->instance()->isBootstrap())->toBeTrue();
    expect($component->instance()->tableTheme)->toBe('bootstrap');
    expect($component->instance()->getHeadClass())->toBe('lt-thead-tinted');
    expect($component->instance()->getFilterBtnClass())->toBe('btn btn-sm rounded-3 lt-btn-subtle d-inline-flex align-items-center gap-1 fw-medium');

    $component->assertSeeHtml('lt-thead-tinted');
});

it('applies tailwind custom classes when theme is tailwind', function () {
    config()->set('livewire-tables.theme', 'tailwind');
    app()->forgetInstance(ThemeManager::class);
    app()->singleton(ThemeManager::class, fn () => new ThemeManager(active: 'tailwind'));

    $component = Livewire::test(BootstrapCustomClassTable::class);

    expect($component->instance()->isTailwind())->toBeTrue();
    expect($component->instance()->getHeadClass())->toBe('bg-sky-50 text-sky-800');
    expect($component->instance()->getFilterLabelClass())->toBe('font-semibold text-sky-700');
});

it('retains bootstrap custom classes after sorting (subsequent request)', function () {
    config()->set('livewire-tables.theme', 'bootstrap');
    app()->forgetInstance(ThemeManager::class);
    app()->singleton(ThemeManager::class, fn () => new ThemeManager(active: 'bootstrap'));

    $component = Livewire::test(BootstrapCustomClassTable::class);

    // Verify initial state
    expect($component->instance()->getHeadClass())->toBe('lt-thead-tinted');

    // Simulate a subsequent request (sort)
    $component->call('sortBy', 'name');

    // Custom classes should still be set after the re-render
    expect($component->instance()->getHeadClass())->toBe('lt-thead-tinted');
    expect($component->instance()->getFilterLabelClass())->toBe('form-label small mb-1 fw-semibold lt-text-700');
    expect($component->instance()->getFilterBtnClass())->toBe('btn btn-sm rounded-3 lt-btn-subtle d-inline-flex align-items-center gap-1 fw-medium');
    $component->assertSeeHtml('lt-thead-tinted');
});

it('custom bootstrap classes survive when ThemeManager is reset to tailwind between requests', function () {
    // Start with bootstrap
    config()->set('livewire-tables.theme', 'bootstrap');
    app()->forgetInstance(ThemeManager::class);
    app()->singleton(ThemeManager::class, fn () => new ThemeManager(active: 'bootstrap'));

    $component = Livewire::test(BootstrapCustomClassTable::class);
    expect($component->instance()->getHeadClass())->toBe('lt-thead-tinted');

    // Simulate parent component resetting ThemeManager to tailwind (like DemoPage does)
    app(ThemeManager::class)->use('tailwind');

    // Trigger a re-render
    $component->call('sortBy', 'name');

    // Custom classes should still be bootstrap because tableTheme is hydrated
    expect($component->instance()->isBootstrap())->toBeTrue();
    expect($component->instance()->getHeadClass())->toBe('lt-thead-tinted');
    expect($component->instance()->getFilterBtnClass())->toBe('btn btn-sm rounded-3 lt-btn-subtle d-inline-flex align-items-center gap-1 fw-medium');
    $component->assertSeeHtml('lt-thead-tinted');
});
