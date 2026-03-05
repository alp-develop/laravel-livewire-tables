<div
    wire:key="filter-{{ $filter->getKey() }}"
    x-data="{
        minVal: '',
        maxVal: '',
        committedMin: '',
        committedMax: '',
        timerMin: null,
        timerMax: null,
        minBound: {{ $filter->getMin() !== null ? $filter->getMin() : 'null' }},
        maxBound: {{ $filter->getMax() !== null ? $filter->getMax() : 'null' }},
        init() {
            let stored = this.$wire.get('tableFilters.{{ $filter->getKey() }}');
            this.minVal = (stored && stored.min !== undefined && stored.min !== null && stored.min !== '') ? String(stored.min) : '';
            this.maxVal = (stored && stored.max !== undefined && stored.max !== null && stored.max !== '') ? String(stored.max) : '';
            this.committedMin = this.minVal;
            this.committedMax = this.maxVal;
            this.$wire.$watch('tableFilters', () => {
                let sv = this.$wire.get('tableFilters.{{ $filter->getKey() }}');
                let nm = (sv && sv.min !== undefined && sv.min !== null && sv.min !== '') ? String(sv.min) : '';
                let nx = (sv && sv.max !== undefined && sv.max !== null && sv.max !== '') ? String(sv.max) : '';
                this.minVal = nm; this.committedMin = nm;
                this.maxVal = nx; this.committedMax = nx;
            });
        },
        commitMin() {
            clearTimeout(this.timerMin);
            let normalized = '';
            if (this.minVal !== '') {
                let n = parseFloat(this.minVal);
                let aboveMin = this.minBound === null || n - this.minBound >= 0;
                let belowMax = this.maxBound === null || this.maxBound - n >= 0;
                if (!isNaN(n) && aboveMin && belowMax) { normalized = this.minVal; }
            }
            this.minVal = normalized;
            if (normalized === this.committedMin) return;
            this.committedMin = normalized;
            this.$wire.set('tableFilters.{{ $filter->getKey() }}.min', normalized);
        },
        debounceMin() {
            clearTimeout(this.timerMin);
            this.timerMin = setTimeout(() => this.commitMin(), 500);
        },
        commitMax() {
            clearTimeout(this.timerMax);
            let normalized = '';
            if (this.maxVal !== '') {
                let n = parseFloat(this.maxVal);
                let aboveMin = this.minBound === null || n - this.minBound >= 0;
                let belowMax = this.maxBound === null || this.maxBound - n >= 0;
                if (!isNaN(n) && aboveMin && belowMax) { normalized = this.maxVal; }
            }
            this.maxVal = normalized;
            if (normalized === this.committedMax) return;
            this.committedMax = normalized;
            this.$wire.set('tableFilters.{{ $filter->getKey() }}.max', normalized);
        },
        debounceMax() {
            clearTimeout(this.timerMax);
            this.timerMax = setTimeout(() => this.commitMax(), 500);
        }
    }"
    class="{{ $classes['filter-range-row'] }}"
>
    <input
        type="number"
        x-model="minVal"
        x-on:input="debounceMin()"
        x-on:change="commitMin()"
        x-on:blur="commitMin()"
        class="{{ $resolvedInputClass }}"
        placeholder="{{ __('livewire-tables::messages.min') }}"
        @if($filter->getMin() !== null) min="{{ $filter->getMin() }}" @endif
        @if($filter->getMax() !== null) max="{{ $filter->getMax() }}" @endif
        @if($filter->getStep() !== null) step="{{ $filter->getStep() }}" @endif
    />
    <span class="{{ $classes['filter-range-separator'] }}">—</span>
    <input
        type="number"
        x-model="maxVal"
        x-on:input="debounceMax()"
        x-on:change="commitMax()"
        x-on:blur="commitMax()"
        class="{{ $resolvedInputClass }}"
        placeholder="{{ __('livewire-tables::messages.max') }}"
        @if($filter->getMin() !== null) min="{{ $filter->getMin() }}" @endif
        @if($filter->getMax() !== null) max="{{ $filter->getMax() }}" @endif
        @if($filter->getStep() !== null) step="{{ $filter->getStep() }}" @endif
    />
</div>
