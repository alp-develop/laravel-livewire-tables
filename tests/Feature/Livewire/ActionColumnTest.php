<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Livewire\Tables\Columns\ActionColumn;
use Livewire\Tables\Livewire\DataTableComponent;

class ActionColModel extends Model
{
    protected $connection = 'testing';

    protected $table = 'action_col_items';

    protected $guarded = [];

    public $timestamps = false;
}

class ActionColComponent extends DataTableComponent
{
    public string $tableKey = 'action-col-test';

    public function query(): Builder
    {
        return ActionColModel::query();
    }

    public function columns(): array
    {
        return [
            ActionColumn::make()
                ->button('Edit', fn ($row) => 'edit('.$row->id.')'),
            ActionColumn::make()
                ->button('Danger', fn ($row) => 'xss(1)"onmouseover="alert(1)'),
        ];
    }
}

beforeEach(function (): void {
    Schema::connection('testing')->create('action_col_items', function ($table): void {
        $table->id();
        $table->string('name');
    });

    ActionColModel::insert([['name' => 'Alice']]);
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('action_col_items');
});

test('ActionColumn renders wire:click without XSS injection in live Livewire component', function (): void {
    $html = Livewire::test(ActionColComponent::class)->html();

    expect($html)->toContain('wire:click')
        ->and($html)->not->toContain('"onmouseover="')
        ->and($html)->toContain('&quot;onmouseover=&quot;');
});

test('ActionColumn renders valid action in live Livewire component', function (): void {
    $html = Livewire::test(ActionColComponent::class)->html();

    expect($html)->toContain('Edit');
});
