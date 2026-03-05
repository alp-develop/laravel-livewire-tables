<div class="{{ $classes['filter-wrapper'] }}">
    @foreach($filters as $filter)
        @php
            $resolvedGroupClass = $filter->getGroupClass() !== '' ? $filter->getGroupClass() : $filterGroupClass;
            $resolvedLabelClass = $filter->getLabelClass() !== '' ? $filter->getLabelClass() : $filterLabelClass;
            $resolvedInputClass = $filterInputClass . ($filter->getInputClass() !== '' ? ' ' . $filter->getInputClass() : '');
            $resolvedSelectClass = $filterSelectClass . ($filter->getInputClass() !== '' ? ' ' . $filter->getInputClass() : '');
        @endphp
        <div class="{{ $resolvedGroupClass }}">
            <label class="{{ $resolvedLabelClass }}">{{ $filter->getLabel() }}</label>
            @include('livewire-tables::components.filters.' . $filter->type(), [
                'resolvedInputClass'  => $resolvedInputClass,
                'resolvedSelectClass' => $resolvedSelectClass,
            ])
        </div>
    @endforeach
    @if($this->hasActiveFilters())
        <div class="{{ $classes['filter-clear-wrapper'] }}">
            <button
                wire:click="clearFilters"
                type="button"
                class="{{ $classes['filter-clear-btn'] }}"
            >{{ __('livewire-tables::messages.clear_all') }}</button>
        </div>
    @endif
</div>
