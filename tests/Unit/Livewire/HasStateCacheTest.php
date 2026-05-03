<?php

declare(strict_types=1);

use Livewire\Tables\Livewire\Concerns\HasStateCache;

function makeStateCacheActor(string $key = ''): object
{
    return new class ($key)
    {
        use HasStateCache;

        public function __construct(string $key)
        {
            $this->tableKey = $key;
        }
    };
}

test('getTableKey uses full md5 of class name when tableKey is empty', function (): void {
    $actor = makeStateCacheActor('');
    $key = $actor->getTableKey();

    expect($key)->toStartWith('lwt_')
        ->and(strlen($key))->toBe(4 + 32);
});

test('getTableKey uses custom key with lwt_ prefix when set', function (): void {
    $actor = makeStateCacheActor('my_table');
    $key = $actor->getTableKey();

    expect($key)->toBe('lwt_my_table');
});

test('getTableKey md5 is full 32 chars not truncated', function (): void {
    $actor = makeStateCacheActor('');
    $key = $actor->getTableKey();
    $hash = substr($key, 4);

    expect(strlen($hash))->toBe(32);
});
