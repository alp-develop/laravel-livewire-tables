<thead class="{{ $headClass }}">
    <tr>
        @if($this->hasBulkActions())
        <th class="{{ $classes['bulk-checkbox-th'] }}">
            <input
                type="checkbox"
                class="{{ $classes['bulk-checkbox'] }}"
                :checked="$wire.selectAllPages
                    ? $wire.pageIds.every(id => !$wire.excludedIds.includes(id))
                    : ($wire.pageIds.length > 0 && $wire.pageIds.every(id => $wire.selectedIds.includes(id)))"
                :indeterminate="$wire.selectAllPages
                    ? ($wire.pageIds.some(id => $wire.excludedIds.includes(id)) && $wire.pageIds.some(id => !$wire.excludedIds.includes(id)))
                    : ($wire.pageIds.some(id => $wire.selectedIds.includes(id)) && !$wire.pageIds.every(id => $wire.selectedIds.includes(id)))"
                x-on:click="
                    const allSelected = $wire.selectAllPages
                        ? $wire.pageIds.every(id => !$wire.excludedIds.includes(id))
                        : ($wire.pageIds.length > 0 && $wire.pageIds.every(id => $wire.selectedIds.includes(id)));
                    allSelected ? $wire.deselectAll() : $wire.selectAllAcrossPages()
                "
            />
        </th>
        @endif
        @foreach($columns as $column)
            <th class="{{ $classes['th'] }} {{ $column->isSortable() ? $classes['th-sortable'] : '' }} {{ $this->isSortedBy($column->field()) ? $classes['th-sorted'] : '' }} {{ $column->getHeaderClass() }}"
                @if($column->getWidth()) style="width: {{ $column->getWidth() }}" @endif
                @if($column->isSortable()) wire:click="sortBy('{{ $column->field() }}')" @endif
            >
                <span style="display:inline-flex;align-items:center;gap:2px;white-space:nowrap">
                    <span>{{ $column->getLabel() }}</span>

                    @if($column->isSortable())
                        @if($this->isSortedBy($column->field()))
                            <span class="{{ $classes['sort-icon-active'] }}">
                                @if($this->getSortDirection($column->field()) === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:14px;height:14px;vertical-align:middle">
                                        <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 01-1.06 0L8 7.06 5.28 9.78a.75.75 0 01-1.06-1.06l3.25-3.25a.75.75 0 011.06 0l3.25 3.25a.75.75 0 010 1.06z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:14px;height:14px;vertical-align:middle">
                                        <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 011.06 0L8 8.94l2.72-2.72a.75.75 0 111.06 1.06l-3.25 3.25a.75.75 0 01-1.06 0L4.22 7.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                @if(count($this->sortFields) > 1)<sup style="font-size:9px">{{ $this->getSortOrder($column->field()) }}</sup>@endif
                            </span>
                        @else
                            <span class="{{ $classes['sort-icon'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:14px;height:14px;vertical-align:middle;opacity:0.3">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 01-1.06 0L8 7.06 5.28 9.78a.75.75 0 01-1.06-1.06l3.25-3.25a.75.75 0 011.06 0l3.25 3.25a.75.75 0 010 1.06z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    @endif
                </span>
            </th>
        @endforeach
    </tr>
</thead>
