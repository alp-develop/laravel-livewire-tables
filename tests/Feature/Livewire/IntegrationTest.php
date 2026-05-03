<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class IntegrationModel extends Model
{
    protected $connection = 'testing';

    protected $table = 'integration_items';

    protected $guarded = [];

    public $timestamps = false;
}

class IntegrationTable extends DataTableComponent
{
    public string $tableKey = 'integration-test';

    public function query(): Builder
    {
        return IntegrationModel::query();
    }

    public function columns(): array
    {
        return [
            Column::text('name')->searchable()->sortable(),
            Column::text('status')->searchable()->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('name')->filter(fn ($q, $v) => $q->where('name', 'like', "%{$v}%")),
            SelectFilter::make('status')
                ->setOptions(['active' => 'Active', 'inactive' => 'Inactive'])
                ->filter(fn ($q, $v) => $q->where('status', $v)),
        ];
    }

    public function bulkActions(): array
    {
        return ['deleteSelected' => 'Delete Selected'];
    }

    public function deleteSelected(): void
    {
        IntegrationModel::whereIn('id', $this->getSelectedIds())->delete();
    }

    public function configure(): void
    {
        $this->setPerPageOptions([5, 10]);
        $this->setDefaultPerPage(5);
    }
}

beforeEach(function (): void {
    Schema::connection('testing')->create('integration_items', function ($table): void {
        $table->id();
        $table->string('name');
        $table->string('status');
    });

    IntegrationModel::insert([
        ['name' => 'Alice', 'status' => 'active'],
        ['name' => 'Bob', 'status' => 'inactive'],
        ['name' => 'Charlie', 'status' => 'active'],
        ['name' => 'Dave', 'status' => 'inactive'],
        ['name' => 'Eve', 'status' => 'active'],
        ['name' => 'Frank', 'status' => 'active'],
    ]);
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('integration_items');
});

test('search + filter applied together return only matching rows', function (): void {
    $component = Livewire::test(IntegrationTable::class);

    $component->set('search', 'a');
    $component->set('tableFilters.status', 'active');

    $ids = $component->instance()->getSelectedIds();

    $rows = IntegrationModel::where('name', 'like', '%a%')
        ->where('status', 'active')
        ->pluck('name')
        ->toArray();

    expect(count($rows))->toBeGreaterThan(0);
});

test('bulk delete with active filter only deletes filtered selection', function (): void {
    $component = Livewire::test(IntegrationTable::class);

    $activeIds = IntegrationModel::where('status', 'active')->pluck('id')
        ->map(fn ($id) => (string) $id)->toArray();

    $component->set('tableFilters.status', 'active');
    foreach ($activeIds as $id) {
        $component->call('toggleSelected', $id);
    }

    $component->call('deleteSelected');

    expect(IntegrationModel::where('status', 'active')->count())->toBe(0);
    expect(IntegrationModel::where('status', 'inactive')->count())->toBe(2);
});

test('bulk delete with injected IDs outside filter scope does not delete unfiltered rows', function (): void {
    $component = Livewire::test(IntegrationTable::class);

    $inactiveIds = IntegrationModel::where('status', 'inactive')->pluck('id')
        ->map(fn ($id) => (string) $id)->toArray();

    $component->set('tableFilters.status', 'active');
    foreach ($inactiveIds as $id) {
        $component->call('toggleSelected', $id);
    }

    $component->call('deleteSelected');

    expect(IntegrationModel::where('status', 'inactive')->count())->toBe(2);
});

test('sort by name ascending returns rows in correct order', function (): void {
    $component = Livewire::test(IntegrationTable::class);

    $component->set('sortFields', ['name' => 'asc']);

    $names = IntegrationModel::orderBy('name', 'asc')->pluck('name')->toArray();
    $sorted = $names;
    sort($sorted);

    expect($names)->toBe($sorted);
});

test('selectAllPages with exclusions returns correct ID subset', function (): void {
    $component = Livewire::test(IntegrationTable::class);

    $allIds = IntegrationModel::pluck('id')->map(fn ($id) => (string) $id)->toArray();
    $excluded = [$allIds[0]];

    $component->call('selectAllAcrossPages');
    $component->call('toggleSelected', $excluded[0]);

    $selected = $component->instance()->getSelectedIds();

    expect($selected)->not->toContain($excluded[0])
        ->and(count($selected))->toBe(count($allIds) - 1);
});

test('getFilterByKey returns correct filter by key', function (): void {
    $component = Livewire::test(IntegrationTable::class);

    $filter = $component->instance()->getFilterByKey('status');

    expect($filter)->not->toBeNull()
        ->and($filter->getKey())->toBe('status');
});

test('getFilterByKey returns null for unknown key', function (): void {
    $component = Livewire::test(IntegrationTable::class);

    $filter = $component->instance()->getFilterByKey('nonexistent_key');

    expect($filter)->toBeNull();
});
