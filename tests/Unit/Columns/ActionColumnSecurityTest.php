<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Livewire\Tables\Columns\ActionColumn;

function makeActionModel(int $id = 1): Model
{
    $model = new class extends Model
    {
        protected $fillable = ['id'];

        protected $table = 'action_security_items';
    };
    $model->id = $id;

    return $model;
}

test('renderCell escapes wire action attribute', function (): void {
    $column = ActionColumn::make()
        ->button('Edit', fn ($row) => 'edit('.$row->id.')"onmouseover="alert(1)');

    $html = $column->renderCell(makeActionModel(5));

    expect($html)->not->toContain('"onmouseover="alert(1)')
        ->and($html)->toContain('&quot;');
});

test('renderCell strips script tags from icon', function (): void {
    $column = ActionColumn::make()
        ->button('Edit', fn ($row) => 'edit(1)', '', '<script>alert(1)</script><svg/>');

    $html = $column->renderCell(makeActionModel(1));

    expect($html)->not->toContain('<script>')
        ->and($html)->not->toContain('alert(1)');
});

test('renderCell strips on-event handlers from icon', function (): void {
    $column = ActionColumn::make()
        ->button('Edit', fn ($row) => 'edit(1)', '', '<img onload="alert(1)" src="x.png">');

    $html = $column->renderCell(makeActionModel(1));

    expect($html)->not->toContain('onload=');
});

test('renderCell escapes class attribute', function (): void {
    $column = ActionColumn::make()
        ->button('Edit', fn ($row) => 'edit(1)', '"class="injected');

    $html = $column->renderCell(makeActionModel(1));

    expect($html)->not->toContain('"class="injected');
});

test('renderCell escapes label', function (): void {
    $column = ActionColumn::make()
        ->button('<img src=x onerror=alert(1)>', fn ($row) => 'edit(1)');

    $html = $column->renderCell(makeActionModel(1));

    expect($html)->not->toContain('<img src=x onerror=alert(1)>')
        ->and($html)->toContain('&lt;img');
});
