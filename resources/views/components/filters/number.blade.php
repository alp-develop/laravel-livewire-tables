<div
    wire:key="filter-{{ $filter->getKey() }}"
    x-data="{
        val: '',
        committed: '',
        timer: null,
        minBound: {{ $filter->getMin() !== null ? $filter->getMin() : 'null' }},
        maxBound: {{ $filter->getMax() !== null ? $filter->getMax() : 'null' }},
        init() {
            let v = this.$wire.get('tableFilters.{{ $filter->getKey() }}');
            this.val = (v !== null && v !== undefined && v !== '') ? String(v) : '';
            this.committed = this.val;
            this.$wire.$watch('tableFilters', () => {
                let sv = this.$wire.get('tableFilters.{{ $filter->getKey() }}');
                let next = (sv !== null && sv !== undefined && sv !== '') ? String(sv) : '';
                this.val = next;
                this.committed = next;
            });
        },
        commit() {
            clearTimeout(this.timer);
            let normalized = '';
            if (this.val !== '' && this.val !== null) {
                let n = parseFloat(this.val);
                let aboveMin = this.minBound === null || n - this.minBound >= 0;
                let belowMax = this.maxBound === null || this.maxBound - n >= 0;
                if (!isNaN(n) && aboveMin && belowMax) { normalized = this.val; }
            }
            this.val = normalized;
            if (normalized === this.committed) return;
            this.committed = normalized;
            this.$wire.set('tableFilters.{{ $filter->getKey() }}', normalized);
        },
        debounce() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.commit(), 500);
        }
    }"
>
    <input
        type="number"
        x-model="val"
        x-on:input="debounce()"
        x-on:change="commit()"
        x-on:blur="commit()"
        class="{{ $resolvedInputClass }}"
        placeholder="{{ $filter->getPlaceholder() }}"
        @if($filter->getMin() !== null) min="{{ $filter->getMin() }}" @endif
        @if($filter->getMax() !== null) max="{{ $filter->getMax() }}" @endif
        @if($filter->getStep() !== null) step="{{ $filter->getStep() }}" @endif
    />
</div>
