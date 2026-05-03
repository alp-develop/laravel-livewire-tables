<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Livewire\DataTableComponent;

class ExportSecModel extends Model
{
    protected $connection = 'testing';

    protected $table = 'export_sec_items';

    protected $guarded = [];

    public $timestamps = false;
}

class ExportSecTableComponent extends DataTableComponent
{
    public string $tableKey = 'export-sec-test';

    public function query(): Builder
    {
        return ExportSecModel::query();
    }

    public function columns(): array
    {
        return [
            Column::text('name')->searchable(),
            Column::text('code'),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('code')->setOptions(['a' => 'A', 'b' => 'B']),
        ];
    }
}

beforeEach(function (): void {
    Schema::connection('testing')->create('export_sec_items', function ($table): void {
        $table->id();
        $table->string('name');
        $table->string('code')->default('a');
    });

    ExportSecModel::insert([
        ['name' => '=CMD|"/C calc"!A0', 'code' => 'a'],
        ['name' => '+malicious', 'code' => 'b'],
        ['name' => '-also-bad', 'code' => 'a'],
        ['name' => '@sum(A1:A9)', 'code' => 'b'],
        ['name' => 'normal value', 'code' => 'a'],
    ]);
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('export_sec_items');
});

test('exportCsvAuto prefixes formula injection characters with apostrophe', function (): void {
    $response = Livewire::test(ExportSecTableComponent::class)
        ->call('exportCsvAuto');

    $streamedContent = '';
    ob_start();
    $response->instance()->exportCsvAuto()->sendContent();
    $streamedContent = ob_get_clean();

    expect($streamedContent)->not->toContain('=CMD|"/C calc"')
        ->and($streamedContent)->toContain("'=CMD|")
        ->and($streamedContent)->toContain("'+malicious")
        ->and($streamedContent)->toContain("'-also-bad")
        ->and($streamedContent)->toContain("'@sum");
});

test('SelectFilter rejects values not in options on single select', function (): void {
    $filter = SelectFilter::make('status')
        ->setOptions(['active' => 'Active', 'inactive' => 'Inactive']);

    $model = new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'export_sec_items';

        protected $guarded = [];

        public $timestamps = false;
    };

    Schema::connection('testing')->create('export_sec_items2', function ($table): void {
        $table->id();
        $table->string('status');
    }) ?: null;

    $builder = $model->setTable('export_sec_items2')->newQuery();
    $result = $filter->apply($builder, 'injected_value');
    $sql = $result->toSql();

    Schema::connection('testing')->dropIfExists('export_sec_items2');

    expect($sql)->not->toContain('where')
        ->and($sql)->not->toContain('injected_value');
});

test('SelectFilter accepts valid value in allowlist', function (): void {
    $filter = SelectFilter::make('code')
        ->setOptions(['a' => 'A', 'b' => 'B']);

    $model = new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'export_sec_items';

        protected $guarded = [];

        public $timestamps = false;
    };

    $builder = $model->newQuery();
    $result = $filter->apply($builder, 'a');

    expect($result->toSql())->toContain('where');
});

test('exportCsvAuto prefixes tab and carriage return injection characters', function (): void {
    ExportSecModel::create(['name' => "\ttab_injection", 'code' => 'a']);
    ExportSecModel::create(['name' => "\rcr_injection", 'code' => 'b']);

    $streamedContent = '';
    ob_start();
    Livewire::test(ExportSecTableComponent::class)
        ->instance()
        ->exportCsvAuto()
        ->sendContent();
    $streamedContent = ob_get_clean();

    expect($streamedContent)->toContain("'")
        ->and($streamedContent)->toContain("'\t")
        ->and($streamedContent)->toContain("'\r");
});
