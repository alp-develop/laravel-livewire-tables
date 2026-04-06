<div>
    {{-- ═══════════════ MAIN CONTENT ═══════════════ --}}
    <main style="max-width:80rem;margin:0 auto;padding:2rem 1.5rem"
          x-data="{ tab: 'products' }">

        {{-- ── Tab Navigation ── --}}
        @if($theme === 'tailwind')
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex gap-6 overflow-x-auto">
                <button @click="tab = 'products'"
                        :class="tab === 'products' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors">
                    {{ __('demo.tab_products') }}
                </button>
                <button @click="tab = 'orders'"
                        :class="tab === 'orders' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors">
                    {{ __('demo.tab_orders') }} <span class="ml-1.5 inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{!! __('demo.badge_joins') !!}</span>
                </button>
                <button @click="tab = 'performance'"
                        :class="tab === 'performance' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors">
                    {{ __('demo.tab_performance') }} <span class="ml-1.5 inline-flex items-center rounded-full bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700">{!! __('demo.badge_bulk') !!}</span>
                </button>
                <button @click="tab = 'catalog'"
                        :class="tab === 'catalog' ? 'border-sky-600 text-sky-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors">
                    {{ __('demo.tab_catalog') }} <span class="ml-1.5 inline-flex items-center rounded-full bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700">{!! __('demo.badge_export') !!}</span>
                </button>
            </nav>
        </div>
        @else
        <ul class="nav nav-tabs" style="margin-bottom:1.5rem">
            <li class="nav-item">
                <button class="nav-link" :class="{ active: tab === 'products' }" @click="tab = 'products'">{{ __('demo.tab_products') }}</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: tab === 'orders' }" @click="tab = 'orders'">
                    {{ __('demo.tab_orders') }} <span class="badge" style="background:#0d6efd;color:#fff;margin-left:0.25rem">{!! __('demo.badge_joins') !!}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: tab === 'performance' }" @click="tab = 'performance'">
                    {{ __('demo.tab_performance') }} <span class="badge" style="background:purple;color:#fff;margin-left:0.25rem">{!! __('demo.badge_bulk_short') !!}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: tab === 'catalog' }" @click="tab = 'catalog'">
                    {{ __('demo.tab_catalog') }} <span class="badge" style="background:#0ea5e9;color:#fff;margin-left:0.25rem">{!! __('demo.badge_export') !!}</span>
                </button>
            </li>
        </ul>
        @endif

        {{-- ═══════════════ PRODUCTS TAB ═══════════════ --}}
        <div x-show="tab === 'products'">
            <div class="lt-section-header">
                <h2 class="lt-section-title">{!! __('demo.products_title') !!}</h2>
                <p class="lt-section-desc">{!! __('demo.products_desc') !!}</p>
            </div>
            <div class="lt-stat-grid">
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $totalProducts }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_total_products') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#059669">{{ $activeProducts }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_active') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $categories }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_categories') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#4f46e5">${{ number_format((float) $avgPrice, 0) }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_avg_price') }}</p>
                </div>
            </div>
            <livewire:products-table :table-theme="$theme" :table-key="'products-' . $theme" />
        </div>

        {{-- ═══════════════ ORDERS TAB ═══════════════ --}}
        <div x-show="tab === 'orders'" x-cloak>
            <div class="lt-section-header">
                <h2 class="lt-section-title">{!! __('demo.orders_title') !!}</h2>
                <p class="lt-section-desc">{!! __('demo.orders_desc') !!}</p>
            </div>
            <div class="lt-stat-grid">
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $totalOrders }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_total_orders') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#059669">{{ $deliveredOrders }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_delivered') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $totalBrands }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_brands') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#4f46e5">${{ number_format((float) $ordersRevenue, 0) }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_total_revenue') }}</p>
                </div>
            </div>
            <livewire:orders-table :param="'test'" :table-theme="$theme" :table-key="'orders-' . $theme" />
        </div>

        {{-- ═══════════════ PERFORMANCE TAB ═══════════════ --}}
        <div x-show="tab === 'performance'" x-cloak>
            <div class="lt-section-header">
                <h2 class="lt-section-title">{!! __('demo.performance_title') !!}</h2>
                <p class="lt-section-desc">{!! __('demo.performance_desc') !!}</p>
            </div>
            <div class="lt-stat-grid">
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $totalEmployees }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_total_employees') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#059669">{{ $activeEmployees }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_active') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $departments }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_departments') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#7c3aed">${{ number_format((float) $avgSalary, 0) }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_avg_salary') }}</p>
                </div>
            </div>
            <livewire:performance-table :table-theme="$theme" :table-key="'performance-' . $theme" />
        </div>

        {{-- ═══════════════ CATALOG TAB ═══════════════ --}}
        <div x-show="tab === 'catalog'" x-cloak>
            <div class="lt-section-header">
                <h2 class="lt-section-title">{!! __('demo.catalog_title') !!}</h2>
                <p class="lt-section-desc">{!! __('demo.catalog_desc') !!}</p>
            </div>
            <div class="lt-stat-grid">
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ number_format($totalCatalog) }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_total_items') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#059669">{{ number_format($activeCatalog) }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_active') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $catalogCategories }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_categories') }}</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#0284c7">${{ number_format((float) $avgCatalogPrice, 2) }}</p>
                    <p class="lt-stat-label">{{ __('demo.stat_avg_price') }}</p>
                </div>
            </div>
            <livewire:catalog-table :table-theme="$theme" :table-key="'catalog-' . $theme" />
        </div>

    </main>
</div>
