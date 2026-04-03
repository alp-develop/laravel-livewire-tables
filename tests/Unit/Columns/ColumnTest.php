<?php

declare(strict_types=1);

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Tables\Columns\BladeColumn;
use Livewire\Tables\Columns\BooleanColumn;
use Livewire\Tables\Columns\Column;
use Livewire\Tables\Columns\DateColumn;
use Livewire\Tables\Columns\ImageColumn;
use Livewire\Tables\Columns\TextColumn;

test('column text factory creates TextColumn', function (): void {
    $column = Column::text('name');

    expect($column)->toBeInstanceOf(TextColumn::class)
        ->and($column->field())->toBe('name')
        ->and($column->getLabel())->toBe('Name');
});

test('column boolean factory creates BooleanColumn', function (): void {
    $column = Column::boolean('active');

    expect($column)->toBeInstanceOf(BooleanColumn::class)
        ->and($column->field())->toBe('active');
});

test('column date factory creates DateColumn', function (): void {
    $column = Column::date('created_at');

    expect($column)->toBeInstanceOf(DateColumn::class)
        ->and($column->field())->toBe('created_at');
});

test('column image factory creates ImageColumn', function (): void {
    $column = Column::image('avatar');

    expect($column)->toBeInstanceOf(ImageColumn::class)
        ->and($column->field())->toBe('avatar');
});

test('column fluent api works', function (): void {
    $column = Column::text('name')
        ->sortable()
        ->searchable()
        ->label('Full Name')
        ->width('200px')
        ->hidden();

    expect($column->isSortable())->toBeTrue()
        ->and($column->isSearchable())->toBeTrue()
        ->and($column->getLabel())->toBe('Full Name')
        ->and($column->getWidth())->toBe('200px')
        ->and($column->isVisible())->toBeFalse();
});

test('column is visible by default', function (): void {
    $column = Column::text('name');

    expect($column->isVisible())->toBeTrue();
});

test('column is not sortable by default', function (): void {
    $column = Column::text('name');

    expect($column->isSortable())->toBeFalse()
        ->and($column->isSearchable())->toBeFalse();
});

test('column generates label from field name', function (): void {
    $column = Column::text('first_name');

    expect($column->getLabel())->toBe('First name');
});

test('TextColumn::make() creates TextColumn', function (): void {
    $column = TextColumn::make('product_name');

    expect($column)->toBeInstanceOf(TextColumn::class)
        ->and($column->field())->toBe('product_name')
        ->and($column->getLabel())->toBe('Product name');
});

test('BooleanColumn::make() creates BooleanColumn', function (): void {
    $column = BooleanColumn::make('active');

    expect($column)->toBeInstanceOf(BooleanColumn::class)
        ->and($column->field())->toBe('active');
});

test('DateColumn::make() creates DateColumn', function (): void {
    $column = DateColumn::make('ordered_at');

    expect($column)->toBeInstanceOf(DateColumn::class)
        ->and($column->field())->toBe('ordered_at');
});

test('ImageColumn::make() creates ImageColumn', function (): void {
    $column = ImageColumn::make('avatar_url');

    expect($column)->toBeInstanceOf(ImageColumn::class)
        ->and($column->field())->toBe('avatar_url');
});

test('dotted field auto-derives label from last segment', function (): void {
    $column = TextColumn::make('brands.name');

    expect($column->getLabel())->toBe('Name')
        ->and($column->field())->toBe('brands.name');
});

test('dotted field with table prefix produces label from column only', function (): void {
    $column = TextColumn::make('orders.unit_price');

    expect($column->getLabel())->toBe('Unit price');
});

test('columnClass is empty by default', function (): void {
    $column = TextColumn::make('name');

    expect($column->getHeaderClass())->toBe('')
        ->and($column->getCellClass())->toBe('');
});

test('columnClass applies to both getHeaderClass and getCellClass', function (): void {
    $column = TextColumn::make('price')->columnClass('col-price');

    expect($column->getHeaderClass())->toBe('!col-price')
        ->and($column->getCellClass())->toBe('!col-price');
});

test('columnClass combines with individual headerClass and cellClass', function (): void {
    $column = TextColumn::make('price')
        ->columnClass('col-price')
        ->headerClass('text-right')
        ->cellClass('font-semibold');

    expect($column->getHeaderClass())->toBe('!col-price !text-right')
        ->and($column->getCellClass())->toBe('!col-price !font-semibold');
});

test('headerClass and cellClass without columnClass work as before', function (): void {
    $column = TextColumn::make('price')
        ->headerClass('text-right')
        ->cellClass('font-bold');

    expect($column->getHeaderClass())->toBe('!text-right')
        ->and($column->getCellClass())->toBe('!font-bold');
});

test('column is not hidden by hideIf by default', function (): void {
    $column = TextColumn::make('name');

    expect($column->isHiddenIf())->toBeFalse();
});

test('hideIf with true hides the column', function (): void {
    $column = TextColumn::make('name')->hideIf(true);

    expect($column->isHiddenIf())->toBeTrue();
});

test('hideIf with false keeps the column visible', function (): void {
    $column = TextColumn::make('name')->hideIf(false);

    expect($column->isHiddenIf())->toBeFalse();
});

test('hideIf is independent from hidden()', function (): void {
    $column = TextColumn::make('name')->hidden()->hideIf(false);

    expect($column->isVisible())->toBeFalse()
        ->and($column->isHiddenIf())->toBeFalse();
});

test('BladeColumn::make creates BladeColumn', function (): void {
    $column = BladeColumn::make();

    expect($column)->toBeInstanceOf(BladeColumn::class)
        ->and($column->type())->toBe('blade')
        ->and($column->isSortable())->toBeFalse()
        ->and($column->isSearchable())->toBeFalse();
});

test('Column::blade factory creates BladeColumn', function (): void {
    $column = Column::blade();

    expect($column)->toBeInstanceOf(BladeColumn::class);
});

test('BladeColumn renderCell returns empty string when no render callback set', function (): void {
    $model = new class extends Model {};
    $column = BladeColumn::make();

    expect($column->renderCell($model))->toBe('');
});

test('BladeColumn renderCell calls render callback with row and table', function (): void {
    $model = new class extends Model {};
    $called = [];

    $column = BladeColumn::make()->render(function ($row, $table) use (&$called): string {
        $called['row'] = $row;
        $called['table'] = $table;

        return '<span>test</span>';
    });

    $result = $column->renderCell($model, 'my-table');

    expect($result)->toBe('<span>test</span>')
        ->and($called['row'])->toBe($model)
        ->and($called['table'])->toBe('my-table');
});

test('BladeColumn renderCell renders Illuminate View instance', function (): void {
    $model = new class extends Model {};

    $view = Mockery::mock(View::class);
    $view->shouldReceive('render')->once()->andReturn('<div>from view</div>');

    $column = BladeColumn::make()->render(fn ($row, $table) => $view);
    $result = $column->renderCell($model);

    expect($result)->toBe('<div>from view</div>');

    Mockery::close();
});

test('BladeColumn resolveValue returns null', function (): void {
    $model = new class extends Model {};
    $column = BladeColumn::make();

    expect($column->resolveValue($model))->toBeNull();
});

test('BladeColumn accepts custom field name', function (): void {
    $column = BladeColumn::make('my_actions');

    expect($column->field())->toBe('my_actions');
});

test('BladeColumn::make without arguments generates unique field', function (): void {
    BladeColumn::resetCounter();
    $column1 = BladeColumn::make();
    $column2 = BladeColumn::make();

    expect($column1->field())->not->toBe($column2->field())
        ->and($column1->field())->toStartWith('_blade_')
        ->and($column2->field())->toStartWith('_blade_');
});

test('searchable accepts custom search field string', function (): void {
    $column = TextColumn::make('brand_name')->searchable('brands.name');

    expect($column->isSearchable())->toBeTrue()
        ->and($column->getSearchField())->toBe('brands.name')
        ->and($column->getSearchCallback())->toBeNull();
});

test('searchable without arguments sets searchable flag only', function (): void {
    $column = TextColumn::make('name')->searchable();

    expect($column->isSearchable())->toBeTrue()
        ->and($column->getSearchField())->toBeNull()
        ->and($column->getSearchCallback())->toBeNull();
});

test('searchable accepts closure callback', function (): void {
    $callback = fn ($query, $search) => $query->orWhere('name', 'LIKE', "%{$search}%");
    $column = TextColumn::make('name')->searchable($callback);

    expect($column->isSearchable())->toBeTrue()
        ->and($column->getSearchCallback())->toBe($callback)
        ->and($column->getSearchField())->toBeNull();
});

test('BladeColumn searchable requires closure to enable searching', function (): void {
    $column = BladeColumn::make()->searchable();

    expect($column->isSearchable())->toBeFalse();
});

test('BladeColumn searchable with closure enables searching', function (): void {
    $callback = fn ($query, $search) => $query->orWhere('status', 'LIKE', "%{$search}%");
    $column = BladeColumn::make()->searchable($callback);

    expect($column->isSearchable())->toBeTrue()
        ->and($column->getSearchCallback())->toBe($callback);
});

test('TextColumn::make without arguments generates unique field', function (): void {
    TextColumn::resetCounter();
    $column1 = TextColumn::make();
    $column2 = TextColumn::make();

    expect($column1->field())->not->toBe($column2->field())
        ->and($column1->field())->toStartWith('_text_')
        ->and($column2->field())->toStartWith('_text_');
});

test('render closure provides computed cell content', function (): void {
    $model = new class extends Model
    {
        protected $guarded = [];
    };
    $model->forceFill(['first_name' => 'John', 'last_name' => 'Doe']);

    $column = TextColumn::make()
        ->render(fn ($row) => $row->first_name.' '.$row->last_name)
        ->label('Full Name');

    expect($column->getLabel())->toBe('Full Name')
        ->and($column->resolveValue($model))->toBe('John Doe');
});

test('label and render work independently', function (): void {
    $model = new class extends Model
    {
        protected $guarded = [];
    };
    $model->forceFill(['name' => 'Test']);

    $column = TextColumn::make()
        ->label('Header')
        ->render(fn ($row) => 'Custom: '.$row->name);

    expect($column->getLabel())->toBe('Header')
        ->and($column->resolveValue($model))->toBe('Custom: Test');
});

test('render closure takes priority over format callback', function (): void {
    $model = new class extends Model
    {
        protected $guarded = [];
    };
    $model->forceFill(['name' => 'Test']);

    $column = TextColumn::make('name')
        ->render(fn ($row) => 'From render: '.$row->name)
        ->format(fn ($value) => 'From format: '.$value);

    expect($column->resolveValue($model))->toBe('From render: Test');
});
