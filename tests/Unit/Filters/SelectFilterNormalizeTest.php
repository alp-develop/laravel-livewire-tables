<?php

declare(strict_types=1);

use Livewire\Tables\Filters\SelectFilter;

test('normalizeValue filters out invalid keys from multi-select', function (): void {
    $filter = SelectFilter::make('status')
        ->multiple()
        ->setOptions(['active' => 'Active', 'inactive' => 'Inactive']);

    $result = $filter->normalizeValue(['active', 'invalid', 'inactive']);

    expect($result)->toBe(['active', 'inactive']);
});

test('normalizeValue accepts numeric string keys matching integer-keyed options', function (): void {
    $filter = SelectFilter::make('price')
        ->multiple()
        ->setOptions(['10' => 'Ten', '20' => 'Twenty']);

    $result = $filter->normalizeValue(['10', '20', '99']);

    expect($result)->toBe(['10', '20']);
});

test('normalizeValue returns original value when not multiple', function (): void {
    $filter = SelectFilter::make('status')
        ->setOptions(['active' => 'Active']);

    $result = $filter->normalizeValue('active');

    expect($result)->toBe('active');
});

test('normalizeValue returns original value when not array', function (): void {
    $filter = SelectFilter::make('status')
        ->multiple()
        ->setOptions(['active' => 'Active']);

    $result = $filter->normalizeValue('active');

    expect($result)->toBe('active');
});

test('normalizeValue returns empty array when all values are invalid', function (): void {
    $filter = SelectFilter::make('status')
        ->multiple()
        ->setOptions(['active' => 'Active']);

    $result = $filter->normalizeValue(['invalid', 'other']);

    expect($result)->toBe([]);
});

test('normalizeValue with int keys in options correctly normalizes string values', function (): void {
    $filter = SelectFilter::make('priority')
        ->multiple()
        ->setOptions([1 => 'Low', 2 => 'Medium', 3 => 'High']);

    $result = $filter->normalizeValue(['1', '2', '99']);

    expect($result)->toBe(['1', '2']);
});

test('normalizeValue with null mixed into array rejects null entries', function (): void {
    $filter = SelectFilter::make('status')
        ->multiple()
        ->setOptions(['active' => 'Active']);

    $result = $filter->normalizeValue(['active', null, '']);

    expect($result)->toBe(['active']);
});
