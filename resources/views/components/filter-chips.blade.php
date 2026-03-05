<div class="{{ $classes['chip-bar'] }}">
    @foreach($sortChips as $chip)
        <span class="{{ $classes['chip'] }}">
            {{ $chip['label'] }}: {{ $chip['direction'] === 'asc' ? '↑ ' . __('livewire-tables::messages.asc') : '↓ ' . __('livewire-tables::messages.desc') }}
            @if(count($sortChips) > 1)<sup style="font-size:9px;opacity:0.7">{{ $chip['order'] }}</sup>@endif
            <button wire:click="clearSortField('{{ $chip['field'] }}')" type="button" class="{{ $classes['chip-remove'] }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:12px;height:12px">
                    <path d="M5.28 4.22a.75.75 0 00-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 101.06 1.06L8 9.06l2.72 2.72a.75.75 0 101.06-1.06L9.06 8l2.72-2.72a.75.75 0 00-1.06-1.06L8 6.94 5.28 4.22z" />
                </svg>
            </button>
        </span>
    @endforeach
    @foreach($activeFilters as $chip)
        <span class="{{ $classes['chip'] }}">
            {{ $chip['label'] }}: {{ $chip['value'] }}
            <button wire:click="removeFilter('{{ $chip['key'] }}')" type="button" class="{{ $classes['chip-remove'] }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:12px;height:12px">
                    <path d="M5.28 4.22a.75.75 0 00-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 101.06 1.06L8 9.06l2.72 2.72a.75.75 0 101.06-1.06L9.06 8l2.72-2.72a.75.75 0 00-1.06-1.06L8 6.94 5.28 4.22z" />
                </svg>
            </button>
        </span>
    @endforeach
    @if(count($activeFilters) > 0)
        <button wire:click="clearFilters" type="button" class="{{ $classes['clear-all-btn'] }}">
            {{ __('livewire-tables::messages.clear_all') }}
        </button>
    @endif
</div>
