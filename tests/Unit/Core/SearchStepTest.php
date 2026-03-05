<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Tables\Columns\BladeColumn;
use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Core\Pipeline\SearchStep;
use Livewire\Tables\Core\State;

beforeEach(function (): void {
    Schema::connection('testing')->create('search_products', function ($table): void {
        $table->id();
        $table->string('name')->nullable();
        $table->string('sku')->nullable();
        $table->unsignedBigInteger('brand_id')->nullable();
    });

    Schema::connection('testing')->create('search_brands', function ($table): void {
        $table->id();
        $table->string('name')->nullable();
        $table->string('country')->nullable();
    });
});

afterEach(function (): void {
    Schema::connection('testing')->dropIfExists('search_products');
    Schema::connection('testing')->dropIfExists('search_brands');
});

function makeSearchModel(): Model
{
    return new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'search_products';

        protected $guarded = [];
    };
}

function makeSearchBrandModel(): Model
{
    return new class extends Model
    {
        protected $connection = 'testing';

        protected $table = 'search_brands';

        protected $guarded = [];
    };
}

function insertSearchData(): void
{
    $brand = makeSearchBrandModel();
    $brand->newQuery()->insert([
        ['id' => 1, 'name' => 'Acme Corp', 'country' => 'USA'],
        ['id' => 2, 'name' => 'Beta Labs', 'country' => 'Germany'],
    ]);

    $product = makeSearchModel();
    $product->newQuery()->insert([
        ['id' => 1, 'name' => 'Widget Alpha', 'sku' => 'WA-001', 'brand_id' => 1],
        ['id' => 2, 'name' => 'Gadget Beta', 'sku' => 'GB-002', 'brand_id' => 2],
        ['id' => 3, 'name' => 'Tool Gamma', 'sku' => 'TG-003', 'brand_id' => 1],
    ]);
}

test('search step searches simple columns', function (): void {
    insertSearchData();

    $columns = [
        TextColumn::make('name')->searchable(),
    ];

    $state = new State(search: 'Widget', sortFields: [], filters: [], perPage: 10, page: 1);
    $builder = makeSearchModel()->newQuery();
    $result = (new SearchStep($columns))->apply($builder, $state);

    expect($result->count())->toBe(1)
        ->and($result->first()->name)->toBe('Widget Alpha');
});

test('search step skips non-searchable columns', function (): void {
    insertSearchData();

    $columns = [
        TextColumn::make('name'),
        TextColumn::make('sku')->searchable(),
    ];

    $state = new State(search: 'Widget', sortFields: [], filters: [], perPage: 10, page: 1);
    $builder = makeSearchModel()->newQuery();
    $result = (new SearchStep($columns))->apply($builder, $state);

    expect($result->count())->toBe(0);
});

test('search step auto-resolves alias from query select', function (): void {
    insertSearchData();

    $columns = [
        TextColumn::make('brand_name')->searchable(),
    ];

    $state = new State(search: 'Acme', sortFields: [], filters: [], perPage: 10, page: 1);

    $builder = makeSearchModel()->newQuery()
        ->join('search_brands', 'search_brands.id', '=', 'search_products.brand_id')
        ->select('search_products.*', 'search_brands.name as brand_name');

    $result = (new SearchStep($columns))->apply($builder, $state);

    expect($result->count())->toBe(2);
});

test('search step works with qualified dot notation column', function (): void {
    insertSearchData();

    $columns = [
        TextColumn::make('search_brands.country')->searchable(),
    ];

    $state = new State(search: 'USA', sortFields: [], filters: [], perPage: 10, page: 1);

    $builder = makeSearchModel()->newQuery()
        ->join('search_brands', 'search_brands.id', '=', 'search_products.brand_id')
        ->select('search_products.*', 'search_brands.country');

    $result = (new SearchStep($columns))->apply($builder, $state);

    expect($result->count())->toBe(2);
});

test('search step uses explicit search field over auto-resolve', function (): void {
    insertSearchData();

    $columns = [
        TextColumn::make('brand_display')->searchable('search_brands.name'),
    ];

    $state = new State(search: 'Beta', sortFields: [], filters: [], perPage: 10, page: 1);

    $builder = makeSearchModel()->newQuery()
        ->join('search_brands', 'search_brands.id', '=', 'search_products.brand_id')
        ->select('search_products.*', 'search_brands.name as brand_display');

    $result = (new SearchStep($columns))->apply($builder, $state);

    expect($result->count())->toBe(1);
});

test('search step uses closure callback for BladeColumn', function (): void {
    insertSearchData();

    $columns = [
        BladeColumn::make()
            ->searchable(fn ($query, $search) => $query->orWhere('sku', 'LIKE', "%{$search}%"))
            ->render(fn ($row) => "<span>{$row->sku}</span>"),
    ];

    $state = new State(search: 'GB-002', sortFields: [], filters: [], perPage: 10, page: 1);
    $builder = makeSearchModel()->newQuery();
    $result = (new SearchStep($columns))->apply($builder, $state);

    expect($result->count())->toBe(1)
        ->and($result->first()->sku)->toBe('GB-002');
});

test('search step returns all results when search is empty', function (): void {
    insertSearchData();

    $columns = [
        TextColumn::make('name')->searchable(),
    ];

    $state = new State(search: '', sortFields: [], filters: [], perPage: 10, page: 1);
    $builder = makeSearchModel()->newQuery();
    $result = (new SearchStep($columns))->apply($builder, $state);

    expect($result->count())->toBe(3);
});
