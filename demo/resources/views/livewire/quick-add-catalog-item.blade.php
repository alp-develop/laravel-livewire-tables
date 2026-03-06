@php
    $activeTheme = app(\Livewire\Tables\Themes\ThemeManager::class)->active();
    $bs = str_starts_with($activeTheme, 'bootstrap');
    $bs4 = $activeTheme === 'bootstrap4';
@endphp
<div>
    @if($bs)
    <button
        type="button"
        wire:click="$set('open', true)"
        class="btn btn-info btn-sm d-inline-flex align-items-center text-white"
        style="gap:.375rem"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
        </svg>
        {{ __('demo.add_item') }}
    </button>
    @else
    <button
        type="button"
        wire:click="$set('open', true)"
        class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-3 py-2 text-sm font-medium text-white hover:bg-sky-700 transition-colors"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
        </svg>
        {{ __('demo.add_item') }}
    </button>
    @endif

    @if($open)
    <div
        x-data
        x-init="$nextTick(() => $el.querySelector('[autofocus]')?.focus())"
        style="position:fixed;inset:0;z-index:50;overflow-y:auto"
        aria-modal="true"
    >
        <div
            style="position:fixed;inset:0;background:rgba(0,0,0,0.5)"
            wire:click="$set('open', false)"
        ></div>

        <div style="position:relative;z-index:10;margin:2rem auto;max-width:34rem;padding:0 1rem">
            <div style="background:var(--lt-bg-card,#fff);border-radius:.75rem;box-shadow:0 20px 25px -5px rgba(0,0,0,.1),0 8px 10px -6px rgba(0,0,0,.1);overflow:hidden">
                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid var(--lt-border,#f3f4f6)">
                    <h3 style="font-size:1rem;font-weight:600;color:var(--lt-text,#111827);margin:0">{{ __('demo.add_catalog_item') }}</h3>
                    <button type="button" wire:click="$set('open', false)" style="color:#9ca3af;background:none;border:none;cursor:pointer;padding:0;line-height:0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit="save" style="padding:1.25rem 1.5rem">
                    <div class="lt-modal-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem">
                        <div style="grid-column:span 2">
                            <label style="display:block;font-size:.75rem;font-weight:500;color:var(--lt-text-muted,#374151);margin-bottom:.25rem">{{ __('demo.field_name') }}</label>
                            <input wire:model="name" type="text" autofocus
                                   class="{{ $bs ? 'form-control' : 'w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none' }}"
                                   placeholder="{{ __('demo.placeholder_name') }}" />
                            @error('name') <p style="font-size:.75rem;color:#ef4444;margin-top:.25rem">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:500;color:var(--lt-text-muted,#374151);margin-bottom:.25rem">{{ __('demo.field_sku') }}</label>
                            <input wire:model="sku" type="text"
                                   class="{{ $bs ? 'form-control' : 'w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none' }}"
                                   placeholder="{{ __('demo.placeholder_sku') }}" />
                            @error('sku') <p style="font-size:.75rem;color:#ef4444;margin-top:.25rem">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:500;color:var(--lt-text-muted,#374151);margin-bottom:.25rem">{{ __('demo.field_brand') }}</label>
                            <input wire:model="brand" type="text"
                                   class="{{ $bs ? 'form-control' : 'w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none' }}"
                                   placeholder="{{ __('demo.placeholder_brand') }}" />
                            @error('brand') <p style="font-size:.75rem;color:#ef4444;margin-top:.25rem">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:500;color:var(--lt-text-muted,#374151);margin-bottom:.25rem">{{ __('demo.field_category') }}</label>
                            <select wire:model="category"
                                    class="{{ $bs ? ($bs4 ? 'custom-select' : 'form-select') : 'w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none bg-white' }}">
                                @foreach($this->categories() as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:500;color:var(--lt-text-muted,#374151);margin-bottom:.25rem">{{ __('demo.field_country') }}</label>
                            <select wire:model="country"
                                    class="{{ $bs ? ($bs4 ? 'custom-select' : 'form-select') : 'w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none bg-white' }}">
                                @foreach($this->countries() as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                            @error('country') <p style="font-size:.75rem;color:#ef4444;margin-top:.25rem">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:500;color:var(--lt-text-muted,#374151);margin-bottom:.25rem">{{ __('demo.field_price') }}</label>
                            <input wire:model="price" type="number" min="0" step="0.01"
                                   class="{{ $bs ? 'form-control' : 'w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none' }}"
                                   placeholder="0.00" />
                            @error('price') <p style="font-size:.75rem;color:#ef4444;margin-top:.25rem">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:500;color:var(--lt-text-muted,#374151);margin-bottom:.25rem">{{ __('demo.field_stock') }}</label>
                            <input wire:model="stock" type="number" min="0"
                                   class="{{ $bs ? 'form-control' : 'w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none' }}"
                                   placeholder="0" />
                            @error('stock') <p style="font-size:.75rem;color:#ef4444;margin-top:.25rem">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:.75rem;padding-top:.75rem;margin-top:.5rem">
                        @if($bs)
                        <button type="button" wire:click="$set('open', false)" class="btn btn-outline-secondary btn-sm">{{ __('demo.cancel') }}</button>
                        <button type="submit" class="btn btn-info btn-sm text-white">
                            <span wire:loading.remove wire:target="save">{{ __('demo.save_item') }}</span>
                            <span wire:loading wire:target="save">{{ __('demo.saving') }}</span>
                        </button>
                        @else
                        <button type="button" wire:click="$set('open', false)"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            {{ __('demo.cancel') }}
                        </button>
                        <button type="submit"
                                class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700 transition-colors">
                            <span wire:loading.remove wire:target="save">{{ __('demo.save_item') }}</span>
                            <span wire:loading wire:target="save">{{ __('demo.saving') }}</span>
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
