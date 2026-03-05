<?php

declare(strict_types=1);

use Livewire\Tables\Filters\BooleanFilter;
use Livewire\Tables\Filters\DateFilter;
use Livewire\Tables\Filters\DateRangeFilter;
use Livewire\Tables\Filters\MultiDateFilter;
use Livewire\Tables\Filters\NumberFilter;
use Livewire\Tables\Filters\NumberRangeFilter;
use Livewire\Tables\Filters\SelectFilter;
use Livewire\Tables\Filters\TextFilter;

test('filter text factory creates TextFilter', function (): void {
    $filter = TextFilter::make('name');

    expect($filter)->toBeInstanceOf(TextFilter::class)
        ->and($filter->field())->toBe('name')
        ->and($filter->type())->toBe('text');
});

test('filter select factory creates SelectFilter', function (): void {
    $filter = SelectFilter::make('role');

    expect($filter)->toBeInstanceOf(SelectFilter::class)
        ->and($filter->type())->toBe('select');
});

test('filter date factory creates DateFilter', function (): void {
    $filter = DateFilter::make('created_at');

    expect($filter)->toBeInstanceOf(DateFilter::class)
        ->and($filter->type())->toBe('date');
});

test('filter boolean factory creates BooleanFilter', function (): void {
    $filter = BooleanFilter::make('active');

    expect($filter)->toBeInstanceOf(BooleanFilter::class)
        ->and($filter->type())->toBe('boolean');
});

test('filter number factory creates NumberFilter', function (): void {
    $filter = NumberFilter::make('age');

    expect($filter)->toBeInstanceOf(NumberFilter::class)
        ->and($filter->type())->toBe('number');
});

test('filter fluent api works', function (): void {
    $filter = TextFilter::make('name')
        ->label('Full Name')
        ->placeholder('Type name...')
        ->default('John');

    expect($filter->getLabel())->toBe('Full Name')
        ->and($filter->getPlaceholder())->toBe('Type name...')
        ->and($filter->defaultValue())->toBe('John');
});

test('select filter accepts options', function (): void {
    $filter = SelectFilter::make('role')
        ->setOptions(['admin' => 'Admin', 'user' => 'User']);

    expect($filter->options())->toBe(['admin' => 'Admin', 'user' => 'User']);
});

test('number filter accepts min and max', function (): void {
    $filter = NumberFilter::make('age')
        ->min(0)
        ->max(120);

    expect($filter->getMin())->toBe(0.0)
        ->and($filter->getMax())->toBe(120.0);
});

test('filter numberRange factory creates NumberRangeFilter', function (): void {
    $filter = NumberRangeFilter::make('price');

    expect($filter)->toBeInstanceOf(NumberRangeFilter::class)
        ->and($filter->field())->toBe('price')
        ->and($filter->type())->toBe('number_range');
});

test('filter dateRange factory creates DateRangeFilter', function (): void {
    $filter = DateRangeFilter::make('created_at');

    expect($filter)->toBeInstanceOf(DateRangeFilter::class)
        ->and($filter->field())->toBe('created_at')
        ->and($filter->type())->toBe('date_range');
});

test('plain field getKey returns field name', function (): void {
    $filter = SelectFilter::make('status');

    expect($filter->getKey())->toBe('status');
});

test('dotted field getKey auto-derives underscore key', function (): void {
    $filter = SelectFilter::make('brands.tier');

    expect($filter->getKey())->toBe('brands_tier')
        ->and($filter->field())->toBe('brands.tier');
});

test('dotted field getKey preserves original field for sql', function (): void {
    $filter = SelectFilter::make('brands.country');

    expect($filter->field())->toBe('brands.country')
        ->and($filter->getKey())->toBe('brands_country');
});

test('explicit key overrides auto-derived dotted key', function (): void {
    $filter = SelectFilter::make('brands.tier')->key('tier');

    expect($filter->getKey())->toBe('tier')
        ->and($filter->field())->toBe('brands.tier');
});

test('dotted field label auto-derived from last segment', function (): void {
    $filter = SelectFilter::make('brands.tier');

    expect($filter->getLabel())->toBe('Tier');
});

test('dotted multi-segment field label from last segment', function (): void {
    $filter = TextFilter::make('brands.country');

    expect($filter->getLabel())->toBe('Country');
});

test('plain field label generated from field name', function (): void {
    $filter = TextFilter::make('unit_price');

    expect($filter->getLabel())->toBe('Unit price');
});

test('DateFilter minDate and maxDate are null by default', function (): void {
    $filter = DateFilter::make('created_at');

    expect($filter->getMinDate())->toBeNull()
        ->and($filter->getMaxDate())->toBeNull();
});

test('DateFilter minDate and maxDate can be set', function (): void {
    $filter = DateFilter::make('created_at')
        ->minDate('2024-01-01')
        ->maxDate('2024-12-31');

    expect($filter->getMinDate())->toBe('2024-01-01')
        ->and($filter->getMaxDate())->toBe('2024-12-31');
});

test('DateRangeFilter default format is Y-m-d', function (): void {
    $filter = DateRangeFilter::make('created_at');

    expect($filter->getFormat())->toBe('Y-m-d');
});

test('DateRangeFilter format can be customized', function (): void {
    $filter = DateRangeFilter::make('created_at')->format('d/m/Y');

    expect($filter->getFormat())->toBe('d/m/Y');
});

test('DateRangeFilter minDate and maxDate can be set', function (): void {
    $filter = DateRangeFilter::make('created_at')
        ->minDate('2024-01-01')
        ->maxDate('2024-12-31');

    expect($filter->getMinDate())->toBe('2024-01-01')
        ->and($filter->getMaxDate())->toBe('2024-12-31');
});

test('DateRangeFilter minDate and maxDate are null by default', function (): void {
    $filter = DateRangeFilter::make('created_at');

    expect($filter->getMinDate())->toBeNull()
        ->and($filter->getMaxDate())->toBeNull();
});

test('NumberFilter step can be set', function (): void {
    $filter = NumberFilter::make('price')->step(0.01);

    expect($filter->getStep())->toBe(0.01);
});

test('NumberFilter step is null by default', function (): void {
    $filter = NumberFilter::make('price');

    expect($filter->getStep())->toBeNull();
});

test('NumberRangeFilter min max step all null by default', function (): void {
    $filter = NumberRangeFilter::make('price');

    expect($filter->getMin())->toBeNull()
        ->and($filter->getMax())->toBeNull()
        ->and($filter->getStep())->toBeNull();
});

test('NumberRangeFilter min max step can be set', function (): void {
    $filter = NumberRangeFilter::make('price')
        ->min(0.0)
        ->max(9999.99)
        ->step(0.01);

    expect($filter->getMin())->toBe(0.0)
        ->and($filter->getMax())->toBe(9999.99)
        ->and($filter->getStep())->toBe(0.01);
});

test('filter style classes are empty by default', function (): void {
    $filter = TextFilter::make('name');

    expect($filter->getGroupClass())->toBe('')
        ->and($filter->getLabelClass())->toBe('')
        ->and($filter->getInputClass())->toBe('');
});

test('filter groupClass can be set', function (): void {
    $filter = TextFilter::make('name')->groupClass('border-b pb-3');

    expect($filter->getGroupClass())->toBe('border-b pb-3');
});

test('filter labelClass can be set', function (): void {
    $filter = SelectFilter::make('status')->labelClass('font-bold text-indigo-700');

    expect($filter->getLabelClass())->toBe('font-bold text-indigo-700');
});

test('filter inputClass can be set', function (): void {
    $filter = NumberFilter::make('price')->inputClass('border-green-400 focus:border-green-600');

    expect($filter->getInputClass())->toBe('border-green-400 focus:border-green-600');
});

test('filter style methods are fluent', function (): void {
    $filter = TextFilter::make('name')
        ->groupClass('group-class')
        ->labelClass('label-class')
        ->inputClass('input-class');

    expect($filter)->toBeInstanceOf(TextFilter::class)
        ->and($filter->getGroupClass())->toBe('group-class')
        ->and($filter->getLabelClass())->toBe('label-class')
        ->and($filter->getInputClass())->toBe('input-class');
});

test('DateRangeFilter calendarClass is null by default', function (): void {
    $filter = DateRangeFilter::make('created_at');

    expect($filter->getCalendarClass())->toBeNull();
});

test('DateRangeFilter calendarClass can be set', function (): void {
    $filter = DateRangeFilter::make('created_at')->calendarClass('cal-orders');

    expect($filter->getCalendarClass())->toBe('cal-orders');
});

test('filterClass is an alias for groupClass', function (): void {
    $filter = TextFilter::make('name')->filterClass('filter-name');

    expect($filter->getGroupClass())->toBe('filter-name');
});

test('NumberFilter getMin and getMax return configured bounds', function (): void {
    $filter = NumberFilter::make('price')->min(5.0)->max(500.0);

    expect($filter->getMin())->toBe(5.0)
        ->and($filter->getMax())->toBe(500.0);
});

test('DateFilter getMinDate and getMaxDate are used for clamping config', function (): void {
    $filter = DateFilter::make('release_date')
        ->minDate('2020-01-01')
        ->maxDate('2027-12-31');

    expect($filter->getMinDate())->toBe('2020-01-01')
        ->and($filter->getMaxDate())->toBe('2027-12-31');
});

test('Filter initialValue is false by default', function (): void {
    $filter = TextFilter::make('name');

    expect($filter->hasInitialValue())->toBeFalse()
        ->and($filter->getInitialValue())->toBeNull();
});

test('Filter initialValue can be set', function (): void {
    $filter = TextFilter::make('name')->initialValue('john');

    expect($filter->hasInitialValue())->toBeTrue()
        ->and($filter->getInitialValue())->toBe('john');
});

test('Filter initialValue accepts array', function (): void {
    $filter = SelectFilter::make('status')->initialValue(['active', 'pending']);

    expect($filter->hasInitialValue())->toBeTrue()
        ->and($filter->getInitialValue())->toBe(['active', 'pending']);
});

test('SelectFilter isMultiple is false by default', function (): void {
    $filter = SelectFilter::make('status');

    expect($filter->isMultiple())->toBeFalse()
        ->and($filter->type())->toBe('select');
});

test('SelectFilter multiple sets isMultiple and changes type to multi_select', function (): void {
    $filter = SelectFilter::make('status')->multiple();

    expect($filter->isMultiple())->toBeTrue()
        ->and($filter->type())->toBe('multi_select');
});

test('SelectFilter isSearchable is false by default', function (): void {
    $filter = SelectFilter::make('status');

    expect($filter->isSearchable())->toBeFalse();
});

test('SelectFilter searchable sets isSearchable', function (): void {
    $filter = SelectFilter::make('status')->multiple()->searchable();

    expect($filter->isSearchable())->toBeTrue();
});

test('SelectFilter normalizeValue removes invalid options when multiple', function (): void {
    $filter = SelectFilter::make('status')
        ->multiple()
        ->setOptions(['active' => 'Active', 'inactive' => 'Inactive']);

    $result = $filter->normalizeValue(['active', 'invalid', 'inactive']);

    expect($result)->toBe(['active', 'inactive']);
});

test('MultiDateFilter type is multi_date', function (): void {
    $filter = MultiDateFilter::make('released_at');

    expect($filter->type())->toBe('multi_date');
});

test('MultiDateFilter default format is Y-m-d', function (): void {
    $filter = MultiDateFilter::make('released_at');

    expect($filter->getFormat())->toBe('Y-m-d');
});

test('MultiDateFilter minDate and maxDate can be set', function (): void {
    $filter = MultiDateFilter::make('released_at')
        ->minDate('2020-01-01')
        ->maxDate('2027-12-31');

    expect($filter->getMinDate())->toBe('2020-01-01')
        ->and($filter->getMaxDate())->toBe('2027-12-31');
});

test('MultiDateFilter normalizeValue removes out-of-bound dates', function (): void {
    $filter = MultiDateFilter::make('released_at')
        ->minDate('2020-01-01')
        ->maxDate('2027-12-31');

    $result = $filter->normalizeValue(['2019-12-31', '2023-06-15', '2028-01-01', '2025-01-01']);

    expect($result)->toBe(['2023-06-15', '2025-01-01']);
});
