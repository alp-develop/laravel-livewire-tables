<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Filters\TextFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class BulkItem extends Model
{
    protected $connection = 'testing';

    protected $table = 'bulk_items';

    protected $guarded = [];

    public $timestamps = false;
}

class BulkTableComponent extends DataTableComponent
{
    public string $tableKey = 'bulk-test';

    public function query(): Builder
    {
        return BulkItem::query();
    }

    public function columns(): array
    {
        return [
            Column::text('name')->searchable(),
        ];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('name')->filter(fn ($q, $v) => $q->where('name', 'like', "%{$v}%")),
        ];
    }

    public function bulkActions(): array
    {
        return ['delete' => 'Delete'];
    }

    public function delete(): void
    {
        BulkItem::whereIn('id', $this->getSelectedIds())->delete();
    }
}

class BulkTableNoActions extends DataTableComponent
{
    public string $tableKey = 'bulk-no-actions';

    public function query(): Builder
    {
        return BulkItem::query();
    }

    public function columns(): array
    {
        return [Column::text('name')];
    }
}

beforeEach(function (): void {
    Schema::connection('testing')->create('bulk_items', function ($table): void {
        $table->id();
        $table->string('name');
    });

    BulkItem::insert([
        ['name' => 'Alpha'],
        ['name' => 'Beta'],
        ['name' => 'Gamma'],
        ['name' => 'Delta'],
        ['name' => 'Epsilon'],
    ]);
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('bulk_items');
});

test('pageIds is populated after render', function (): void {
    $component = Livewire::test(BulkTableComponent::class);

    expect($component->get('pageIds'))->toHaveCount(5);
});

test('pageIds contains string values', function (): void {
    $component = Livewire::test(BulkTableComponent::class);

    foreach ($component->get('pageIds') as $id) {
        expect($id)->toBeString();
    }
});

test('pageIds is empty for component without bulk actions', function (): void {
    $component = Livewire::test(BulkTableNoActions::class);

    expect($component->get('pageIds'))->toBe([]);
});

test('executeBulkAction with specific ids deletes correct rows', function (): void {
    $ids = BulkItem::whereIn('name', ['Alpha', 'Beta'])->pluck('id')->map(fn ($id) => (string) $id)->toArray();

    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectedIds', $ids);
    $component->call('executeBulkAction', 'delete');

    expect(BulkItem::count())->toBe(3)
        ->and(BulkItem::where('name', 'Alpha')->exists())->toBeFalse()
        ->and(BulkItem::where('name', 'Beta')->exists())->toBeFalse();
});

test('executeBulkAction resets selectedIds after execution', function (): void {
    $ids = BulkItem::pluck('id')->map(fn ($id) => (string) $id)->toArray();

    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectedIds', $ids);
    $component->call('executeBulkAction', 'delete');

    expect($component->get('selectedIds'))->toBe([])
        ->and($component->get('selectAllPages'))->toBeFalse();
});

test('executeBulkAction with selectAllPages deletes all rows', function (): void {
    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectAllPages', true);
    $component->call('executeBulkAction', 'delete');

    expect(BulkItem::count())->toBe(0);
});

test('executeBulkAction with selectAllPages and active filter deletes only filtered rows', function (): void {
    $component = Livewire::test(BulkTableComponent::class);
    $component->set('tableFilters', ['name' => 'Alpha']);
    $component->set('selectAllPages', true);
    $component->call('executeBulkAction', 'delete');

    expect(BulkItem::count())->toBe(4)
        ->and(BulkItem::where('name', 'Alpha')->exists())->toBeFalse();
});

test('executeBulkAction does nothing when component has no bulk actions', function (): void {
    $component = Livewire::test(BulkTableNoActions::class);
    $component->set('selectedIds', ['1', '2']);

    expect(fn () => $component->call('executeBulkAction', 'delete'))->not->toThrow(Exception::class);
    expect(BulkItem::count())->toBe(5);
});

test('deselectAll via Livewire resets state', function (): void {
    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectedIds', ['1', '2', '3']);
    $component->set('selectAllPages', true);
    $component->call('deselectAll');

    expect($component->get('selectedIds'))->toBe([])
        ->and($component->get('excludedIds'))->toBe([])
        ->and($component->get('selectAllPages'))->toBeFalse();
});

test('executeBulkAction with selectAllPages and excludedIds skips excluded rows', function (): void {
    $excludedId = (string) BulkItem::where('name', 'Alpha')->value('id');

    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectAllPages', true);
    $component->set('excludedIds', [$excludedId]);
    $component->call('executeBulkAction', 'delete');

    expect(BulkItem::count())->toBe(1)
        ->and(BulkItem::where('name', 'Alpha')->exists())->toBeTrue();
});

test('executeBulkAction does nothing for unknown action', function (): void {
    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectedIds', ['1', '2']);

    expect(fn () => $component->call('executeBulkAction', 'nonexistent'))->not->toThrow(Exception::class);
    expect(BulkItem::count())->toBe(5);
});

test('executeBulkAction resets excludedIds after execution', function (): void {
    $excludedId = (string) BulkItem::where('name', 'Alpha')->value('id');

    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectAllPages', true);
    $component->set('excludedIds', [$excludedId]);
    $component->call('executeBulkAction', 'delete');

    expect($component->get('excludedIds'))->toBe([])
        ->and($component->get('selectAllPages'))->toBeFalse();
});

test('getSelectedIds returns selectedIds when selectAllPages is false', function (): void {
    $ids = BulkItem::whereIn('name', ['Alpha', 'Beta'])->pluck('id')->map(fn ($id) => (string) $id)->toArray();

    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectedIds', $ids);

    expect($component->instance()->getSelectedIds())->toEqualCanonicalizing($ids);
});

test('getSelectedIds returns all ids minus excluded when selectAllPages is true', function (): void {
    $all = BulkItem::pluck('id')->map(fn ($id) => (string) $id)->toArray();
    $exclude = [(string) BulkItem::where('name', 'Alpha')->value('id')];
    $expect = array_values(array_diff($all, $exclude));

    $component = Livewire::test(BulkTableComponent::class);
    $component->set('selectAllPages', true);
    $component->set('excludedIds', $exclude);

    expect($component->instance()->getSelectedIds())->toEqualCanonicalizing($expect);
});
