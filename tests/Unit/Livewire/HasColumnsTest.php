<?php

declare(strict_types=1);

use Livewire\Tables\Columns\TextColumn;
use Livewire\Tables\Core\Contracts\ColumnContract;
use Livewire\Tables\Livewire\Concerns\HasBulkActions;
use Livewire\Tables\Livewire\Concerns\HasColumns;

function makeColumnsActor(array $cols, array $hidden = []): object
{
    return new class ($cols, $hidden)
    {
        use HasBulkActions;
        use HasColumns;

        public function __construct(private array $cols, array $hidden)
        {
            $this->hiddenColumns = $hidden;
        }

        public function columns(): array
        {
            return $this->cols;
        }

        public function bulkActions(): array
        {
            return [];
        }
    };
}

test('getAllColumns returns visible non-hiddenIf columns', function (): void {
    $colA = TextColumn::make('name', 'Name');
    $colB = TextColumn::make('email', 'Email');

    $actor = makeColumnsActor([$colA, $colB]);

    expect($actor->getAllColumns())->toHaveCount(2);
});

test('getAllColumns returns same instance on repeated calls (cached)', function (): void {
    $calls = 0;

    $actor = new class ($calls)
    {
        use HasBulkActions;
        use HasColumns;

        public function __construct(private int &$calls)
        {
            $this->hiddenColumns = [];
        }

        public function columns(): array
        {
            $this->calls++;

            return [TextColumn::make('name', 'Name')];
        }

        public function bulkActions(): array
        {
            return [];
        }
    };

    $actor->getAllColumns();
    $actor->getAllColumns();
    $actor->getAllColumns();

    expect($calls)->toBe(1);
});
