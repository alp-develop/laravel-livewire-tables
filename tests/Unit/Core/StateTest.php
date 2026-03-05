<?php

declare(strict_types=1);

use Livewire\Tables\Core\State;

test('state has default values', function (): void {
    $state = new State;

    expect($state->search())->toBe('')
        ->and($state->sortFields())->toBe([])
        ->and($state->filters())->toBe([])
        ->and($state->perPage())->toBe(10)
        ->and($state->page())->toBe(1);
});

test('state accepts custom values', function (): void {
    $state = new State(
        search: 'John',
        sortFields: ['name' => 'desc', 'sku' => 'asc'],
        filters: ['active' => true],
        perPage: 25,
        page: 3,
    );

    expect($state->search())->toBe('John')
        ->and($state->sortFields())->toBe(['name' => 'desc', 'sku' => 'asc'])
        ->and($state->filters())->toBe(['active' => true])
        ->and($state->perPage())->toBe(25)
        ->and($state->page())->toBe(3);
});
