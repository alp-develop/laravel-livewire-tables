<div style="position:relative" x-data="{ get value() { return $wire.get('tableFilters.{{ $filter->getKey() }}') || '' } }">
    <input
        type="text"
        wire:model.live.debounce.500ms="tableFilters.{{ $filter->getKey() }}"
        class="{{ $resolvedInputClass }}"
        placeholder="{{ $filter->getPlaceholder() }}"
        style="padding-right:2rem"
    />
    <button
        type="button"
        x-show="value !== ''"
        x-cloak
        x-on:click.stop="$wire.set('tableFilters.{{ $filter->getKey() }}', '')"
        style="position:absolute;right:0.5rem;top:50%;transform:translateY(-50%);border:none;background:none;cursor:pointer;color:#9ca3af;padding:0;display:flex;align-items:center"
        onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#9ca3af'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
        </svg>
    </button>
</div>
