<tbody class="{{ $bodyClass }}">
    @forelse($rows as $row)
        <tr class="{{ $classes['tr'] }} {{ $this->resolveRowClass($row) }}">
            @if($this->hasBulkActions())
            <td class="{{ $classes['bulk-checkbox-td'] }}">
                @php $rowId = \Illuminate\Support\Js::from((string) data_get($row, $row->getKeyName())); @endphp
                <input
                    type="checkbox"
                    class="{{ $classes['bulk-checkbox'] }}"
                    :checked="$wire.selectAllPages
                        ? !$wire.excludedIds.includes({{ $rowId }})
                        : $wire.selectedIds.includes({{ $rowId }})"
                    x-on:click="$wire.toggleSelected({{ $rowId }})"
                />
            </td>
            @endif
            @foreach($columns as $column)
                <td class="{{ $classes['td'] }} {{ $column->getCellClass() }}">
                    @if($column->type() === 'blade')
                        @include('livewire-tables::components.columns.blade', ['row' => $row, 'column' => $column, 'table' => $this])
                    @elseif($column->type() === 'action')
                        {!! $column->renderCell($row, $this) !!}
                    @elseif($column->getView())
                        @include($column->getView(), ['row' => $row, 'column' => $column, 'table' => $this])
                    @else
                        @include('livewire-tables::components.columns.' . $column->type(), ['row' => $row, 'column' => $column])
                    @endif
                </td>
            @endforeach
        </tr>
    @empty
        <tr>
            <td colspan="{{ count($columns) + ($this->hasBulkActions() ? 1 : 0) }}" class="{{ $classes['empty-state'] }}">
                {{ $this->getEmptyMessage() }}
            </td>
        </tr>
    @endforelse
</tbody>
