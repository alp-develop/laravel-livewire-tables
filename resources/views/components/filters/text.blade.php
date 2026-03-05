<input
    type="text"
    wire:model.live="tableFilters.{{ $filter->getKey() }}"
    class="{{ $resolvedInputClass }}"
    placeholder="{{ $filter->getPlaceholder() }}"
/>
