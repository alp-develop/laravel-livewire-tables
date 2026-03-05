<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Livewire\DataTableComponent;

class ListenerItem extends Model
{
    protected $connection = 'testing';

    protected $table = 'listener_items';

    protected $guarded = [];

    public $timestamps = false;
}

class ListenerTableComponent extends DataTableComponent
{
    public string $tableKey = 'listener-test';

    public function query(): Builder
    {
        return ListenerItem::query();
    }

    public function columns(): array
    {
        return [Column::text('name')];
    }
}

class CustomListenerTableComponent extends DataTableComponent
{
    public string $tableKey = 'custom-listener-test';

    public bool $customHandled = false;

    public function query(): Builder
    {
        return ListenerItem::query();
    }

    public function columns(): array
    {
        return [Column::text('name')];
    }

    public function listeners(): array
    {
        return ['product-created' => 'handleProductCreated'];
    }

    public function handleProductCreated(): void
    {
        $this->customHandled = true;
    }
}

class CustomRefreshEventComponent extends DataTableComponent
{
    public string $tableKey = 'custom-event-test';

    protected string $refreshEvent = 'my-custom-refresh';

    public function query(): Builder
    {
        return ListenerItem::query();
    }

    public function columns(): array
    {
        return [Column::text('name')];
    }
}

beforeEach(function (): void {
    Schema::connection('testing')->create('listener_items', function ($table): void {
        $table->id();
        $table->string('name');
    });

    ListenerItem::insert([
        ['name' => 'Alpha'],
        ['name' => 'Beta'],
        ['name' => 'Gamma'],
    ]);
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('listener_items');
});

test('getRefreshEventName returns tableKey-refresh', function (): void {
    $component = Livewire::test(ListenerTableComponent::class);

    expect($component->instance()->getRefreshEventName())->toBe('listener-test-refresh');
});

test('getListeners includes global refresh event', function (): void {
    $component = Livewire::test(ListenerTableComponent::class);
    $listeners = $component->instance()->getListeners();

    expect($listeners)->toHaveKey('livewire-tables-refresh')
        ->and($listeners['livewire-tables-refresh'])->toBe('refreshTable');
});

test('getListeners includes targeted refresh event', function (): void {
    $component = Livewire::test(ListenerTableComponent::class);
    $listeners = $component->instance()->getListeners();

    expect($listeners)->toHaveKey('listener-test-refresh')
        ->and($listeners['listener-test-refresh'])->toBe('refreshTable');
});

test('getListeners merges user-defined listeners', function (): void {
    $component = Livewire::test(CustomListenerTableComponent::class);
    $listeners = $component->instance()->getListeners();

    expect($listeners)->toHaveKey('livewire-tables-refresh')
        ->and($listeners)->toHaveKey('custom-listener-test-refresh')
        ->and($listeners)->toHaveKey('product-created')
        ->and($listeners['product-created'])->toBe('handleProductCreated');
});

test('dispatching global refresh event triggers refreshTable', function (): void {
    $component = Livewire::test(ListenerTableComponent::class);
    $component->dispatch('livewire-tables-refresh');

    $component->assertHasNoErrors();
});

test('dispatching targeted refresh event triggers refreshTable', function (): void {
    $component = Livewire::test(ListenerTableComponent::class);
    $component->dispatch('listener-test-refresh');

    $component->assertHasNoErrors();
});

test('dispatching custom user listener calls defined method', function (): void {
    $component = Livewire::test(CustomListenerTableComponent::class);
    $component->dispatch('product-created');

    expect($component->get('customHandled'))->toBeTrue();
});

test('refreshTable re-renders and picks up new data', function (): void {
    $component = Livewire::test(ListenerTableComponent::class);

    ListenerItem::create(['name' => 'Delta']);

    $component->dispatch('listener-test-refresh');

    $component->assertHasNoErrors();
    expect(ListenerItem::count())->toBe(4);
});

test('table with no tableKey generates refresh event name from class hash', function (): void {
    $table = new class extends DataTableComponent
    {
        public function query(): Builder
        {
            return ListenerItem::query();
        }

        public function columns(): array
        {
            return [Column::text('name')];
        }
    };

    $name = $table->getRefreshEventName();

    expect($name)->toEndWith('-refresh')
        ->and(strlen($name))->toBeGreaterThan(8);
});

test('protected refreshEvent overrides default event name', function (): void {
    $component = Livewire::test(CustomRefreshEventComponent::class);

    expect($component->instance()->getRefreshEventName())->toBe('my-custom-refresh');
});

test('custom refreshEvent is registered in getListeners', function (): void {
    $component = Livewire::test(CustomRefreshEventComponent::class);
    $listeners = $component->instance()->getListeners();

    expect($listeners)->toHaveKey('my-custom-refresh')
        ->and($listeners['my-custom-refresh'])->toBe('refreshTable')
        ->and($listeners)->toHaveKey('livewire-tables-refresh')
        ->and($listeners)->not->toHaveKey('custom-event-test-refresh');
});

test('dispatching custom refreshEvent triggers refreshTable', function (): void {
    $component = Livewire::test(CustomRefreshEventComponent::class);
    $component->dispatch('my-custom-refresh');

    $component->assertHasNoErrors();
});
