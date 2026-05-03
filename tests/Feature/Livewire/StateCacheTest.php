<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class StateCacheItem extends Model
{
    protected $connection = 'testing';

    protected $table = 'state_cache_items';

    protected $guarded = [];

    public $timestamps = false;
}

class StateCacheTableComponent extends DataTableComponent
{
    public string $tableKey = 'state-cache-test';

    public function query(): Builder
    {
        return StateCacheItem::query();
    }

    public function columns(): array
    {
        return [
            Column::text('name')->sortable()->searchable(),
        ];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('name')->filter(fn ($q, $v) => $q->where('name', 'like', "%{$v}%")),
        ];
    }
}

beforeEach(function (): void {
    Schema::connection('testing')->create('state_cache_items', function ($table): void {
        $table->id();
        $table->string('name');
    });

    StateCacheItem::insert([
        ['name' => 'Alpha'],
        ['name' => 'Beta'],
    ]);
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('state_cache_items');
    Session::forget('livewire_tables');
});

test('dehydrate saves state to session after render', function (): void {
    Livewire::test(StateCacheTableComponent::class);

    $state = Session::get('livewire_tables.lwt_state-cache-test');

    expect($state)->toBeArray()
        ->and($state)->toHaveKey('search')
        ->and($state)->toHaveKey('sortFields')
        ->and($state)->toHaveKey('tableFilters')
        ->and($state)->toHaveKey('perPage');
});

test('loadStateFromCache restores search from session', function (): void {
    Session::put('livewire_tables.lwt_state-cache-test', [
        'search' => 'Alpha',
        'sortFields' => [],
        'tableFilters' => [],
        'perPage' => 10,
        'hiddenColumns' => [],
    ]);

    $component = Livewire::test(StateCacheTableComponent::class);

    expect($component->get('search'))->toBe('Alpha');
});

test('loadStateFromCache ignores invalid sort direction from session', function (): void {
    Session::put('livewire_tables.lwt_state-cache-test', [
        'search' => '',
        'sortFields' => ['name' => 'HACKED_DIRECTION'],
        'tableFilters' => [],
        'perPage' => 10,
        'hiddenColumns' => [],
    ]);

    $component = Livewire::test(StateCacheTableComponent::class);
    $sortFields = $component->get('sortFields');

    expect($sortFields['name'])->toBe('asc');
});

test('loadStateFromCache ignores unknown sort fields from session', function (): void {
    Session::put('livewire_tables.lwt_state-cache-test', [
        'search' => '',
        'sortFields' => ['nonexistent_column' => 'asc'],
        'tableFilters' => [],
        'perPage' => 10,
        'hiddenColumns' => [],
    ]);

    $component = Livewire::test(StateCacheTableComponent::class);
    $sortFields = $component->get('sortFields');

    expect($sortFields)->not->toHaveKey('nonexistent_column');
});

test('loadStateFromCache ignores unknown filter keys from session', function (): void {
    Session::put('livewire_tables.lwt_state-cache-test', [
        'search' => '',
        'sortFields' => [],
        'tableFilters' => ['__injected_filter' => 'malicious'],
        'perPage' => 10,
        'hiddenColumns' => [],
    ]);

    $component = Livewire::test(StateCacheTableComponent::class);
    $filters = $component->get('tableFilters');

    expect($filters)->not->toHaveKey('__injected_filter');
});

test('dehydrate does not write session when state is unchanged', function (): void {
    $component = Livewire::test(StateCacheTableComponent::class);

    $state1 = Session::get('livewire_tables.lwt_state-cache-test');

    $component->call('$refresh');

    $state2 = Session::get('livewire_tables.lwt_state-cache-test');

    expect($state1)->toBe($state2);
});

test('loadStateFromCache ignores non-array session data', function (): void {
    Session::put('livewire_tables.lwt_state-cache-test', 'corrupted_string');

    $component = Livewire::test(StateCacheTableComponent::class);

    expect($component->get('search'))->toBe('');
});
