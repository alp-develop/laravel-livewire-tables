<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Livewire\Tables\Columns\BladeColumn;

function makeBladeModel(): Model
{
    return new class extends Model
    {
        protected $fillable = ['id', 'name'];

        protected $table = 'blade_column_items';

        public $timestamps = false;
    };
}

test('BladeColumn renders HTML from callback unescaped (intentional design)', function (): void {
    $column = BladeColumn::make('name')
        ->render(fn ($row) => '<strong>'.$row->name.'</strong>');

    $model = makeBladeModel();
    $model->name = 'Alice';

    $html = $column->renderCell($model);

    expect($html)->toBe('<strong>Alice</strong>');
});

test('BladeColumn developer must escape user data to prevent XSS (dev responsibility)', function (): void {
    $userInput = '<script>alert(1)</script>';

    $safeColumn = BladeColumn::make('name')
        ->render(fn ($row) => '<div>'.e($row->name).'</div>');

    $model = makeBladeModel();
    $model->name = $userInput;

    $html = $safeColumn->renderCell($model);

    expect($html)->not->toContain('<script>')
        ->and($html)->toContain('&lt;script&gt;');
});

test('BladeColumn without escaping exposes XSS if dev is careless', function (): void {
    $userInput = '<img onload="alert(1)" src="x">';

    $unsafeColumn = BladeColumn::make('name')
        ->render(fn ($row) => '<div>'.$row->name.'</div>');

    $model = makeBladeModel();
    $model->name = $userInput;

    $html = $unsafeColumn->renderCell($model);

    expect($html)->toContain('onload=');
});
