<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Livewire\DataTableComponent;

class ValidationModel extends Model
{
    protected $connection = 'testing';

    protected $table = 'validation_items';

    protected $guarded = [];

    public $timestamps = false;
}

class ValidationTableComponent extends DataTableComponent
{
    public string $tableKey = 'validation-test';

    public function query(): Builder
    {
        return ValidationModel::query();
    }

    public function columns(): array
    {
        return [
            Column::text('name')->sortable()->searchable(),
        ];
    }

    public function configure(): void
    {
        $this->setPerPageOptions([10, 25, 50]);
        $this->setDefaultPerPage(10);
    }
}

beforeEach(function (): void {
    Schema::connection('testing')->create('validation_items', function ($table): void {
        $table->id();
        $table->string('name');
    });

    ValidationModel::insert([
        ['name' => 'Alpha'],
        ['name' => 'Beta'],
    ]);
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('validation_items');
});

test('perPage is reset to default when invalid value is set', function (): void {
    $component = Livewire::test(ValidationTableComponent::class);

    $component->set('perPage', 9999);

    expect($component->get('perPage'))->toBe(10);
});

test('perPage accepts valid whitelisted value', function (): void {
    $component = Livewire::test(ValidationTableComponent::class);

    $component->set('perPage', 25);

    expect($component->get('perPage'))->toBe(25);
});

test('SortStep ignores sort field not in columns whitelist via session', function (): void {
    Session::put('livewire_tables.lwt_validation-test', [
        'search' => '',
        'sortFields' => ['nonexistent_injected_field' => 'asc'],
        'tableFilters' => [],
        'perPage' => 10,
        'hiddenColumns' => [],
    ]);

    $component = Livewire::test(ValidationTableComponent::class);
    $sortFields = $component->get('sortFields');

    expect($sortFields)->not->toHaveKey('nonexistent_injected_field');
});

test('getSelectedIds rejects IDs not present in query result set (TOCTOU protection)', function (): void {
    $component = Livewire::test(ValidationTableComponent::class);

    $existingIds = ValidationModel::pluck('id')->map(fn ($id) => (string) $id)->toArray();
    $fakeId = '99999';

    $component->call('toggleSelected', $existingIds[0]);
    $component->call('toggleSelected', $fakeId);

    $selected = $component->instance()->getSelectedIds();

    expect($selected)->toContain($existingIds[0])
        ->and($selected)->not->toContain($fakeId);
});

test('getSelectedIds returns only intersection with database regardless of injected extra IDs', function (): void {
    $component = Livewire::test(ValidationTableComponent::class);

    $existingIds = ValidationModel::pluck('id')->map(fn ($id) => (string) $id)->toArray();
    $fakeIds = ['88888', '99999', '77777'];

    foreach (array_merge($existingIds, $fakeIds) as $id) {
        $component->call('toggleSelected', $id);
    }

    $selected = $component->instance()->getSelectedIds();

    foreach ($fakeIds as $fakeId) {
        expect($selected)->not->toContain($fakeId);
    }

    expect(count($selected))->toBeLessThanOrEqual(count($existingIds));
});
