<div class="{{ $classes['search-wrapper'] }}" x-data style="position:relative">
    <div class="{{ $classes['search-icon'] }}" style="position:absolute;top:50%;left:0;transform:translateY(-50%);margin-left:0.5rem;pointer-events:none;display:flex;align-items:center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
        </svg>
    </div>
    <input
        type="text"
        wire:model.live.debounce.{{ $this->getSearchDebounce() }}ms="search"
        placeholder="{{ __('livewire-tables::messages.search') }}"
        class="{{ $classes['search-input'] }}"
        style="padding-right:2rem"
    />
    <button
        x-show="$wire.search !== ''"
        x-cloak
        type="button"
        wire:click="clearSearch"
        style="position:absolute;right:0.5rem;top:50%;transform:translateY(-50%);display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;padding:2px;opacity:0.5;transition:opacity 0.15s"
        onmouseover="this.style.opacity='1'"
        onmouseout="this.style.opacity='0.5'"
        title="{{ __('livewire-tables::messages.search') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:14px;height:14px">
            <path d="M5.28 4.22a.75.75 0 00-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 101.06 1.06L8 9.06l2.72 2.72a.75.75 0 101.06-1.06L9.06 8l2.72-2.72a.75.75 0 00-1.06-1.06L8 6.94 5.28 4.22z" />
        </svg>
    </button>
</div>
