@php $bs = str_starts_with(app(\Livewire\Tables\Themes\ThemeManager::class)->active(), 'bootstrap'); @endphp
<div style="display:flex;align-items:center;gap:.5rem">
    @if($item->active)
        @if($bs)
        <span class="badge badge-success" style="font-size:.75rem;padding:.35em .65em">Active</span>
        @else
        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-700/10">
            Active
        </span>
        @endif
    @else
        @if($bs)
        <span class="badge badge-danger" style="font-size:.75rem;padding:.35em .65em">Inactive</span>
        @else
        <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-600 ring-1 ring-inset ring-red-600/10">
            Inactive
        </span>
        @endif
    @endif

    @if($bs)
    <button
        wire:click="toggleActive({{ $item->id }})"
        wire:loading.attr="disabled"
        wire:target="toggleActive({{ $item->id }})"
        type="button"
        class="btn btn-sm {{ $item->active ? 'btn-outline-danger' : 'btn-outline-success' }}"
        style="font-size:.75rem;padding:.2rem .5rem"
    >
        <span wire:loading.remove wire:target="toggleActive({{ $item->id }})">
            {{ $item->active ? 'Deactivate' : 'Activate' }}
        </span>
        <span wire:loading wire:target="toggleActive({{ $item->id }})">...</span>
    </button>
    @else
    <button
        wire:click="toggleActive({{ $item->id }})"
        wire:loading.attr="disabled"
        wire:target="toggleActive({{ $item->id }})"
        type="button"
        class="inline-flex items-center rounded px-2 py-1 text-xs font-medium transition-colors cursor-pointer border
               {{ $item->active
                    ? 'text-red-600 border-red-200 hover:bg-red-50'
                    : 'text-emerald-600 border-emerald-200 hover:bg-emerald-50' }}"
    >
        <span wire:loading.remove wire:target="toggleActive({{ $item->id }})">
            {{ $item->active ? 'Deactivate' : 'Activate' }}
        </span>
        <span wire:loading wire:target="toggleActive({{ $item->id }})">...</span>
    </button>
    @endif
</div>
