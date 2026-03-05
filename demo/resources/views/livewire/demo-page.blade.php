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
                    Products
                </button>
                <button @click="tab = 'orders'"
                        :class="tab === 'orders' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors">
                    Orders <span class="ml-1.5 inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">Joins</span>
                </button>
                <button @click="tab = 'performance'"
                        :class="tab === 'performance' ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors">
                    Performance <span class="ml-1.5 inline-flex items-center rounded-full bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700">1k &middot; Bulk</span>
                </button>
                <button @click="tab = 'catalog'"
                        :class="tab === 'catalog' ? 'border-sky-600 text-sky-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors">
                    Catalog <span class="ml-1.5 inline-flex items-center rounded-full bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700">12k &middot; Export</span>
                </button>
            </nav>
        </div>
        @else
        <ul class="nav nav-tabs" style="margin-bottom:1.5rem">
            <li class="nav-item">
                <button class="nav-link" :class="{ active: tab === 'products' }" @click="tab = 'products'">Products</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: tab === 'orders' }" @click="tab = 'orders'">
                    Orders <span class="badge" style="background:#0d6efd;color:#fff;margin-left:0.25rem">Joins</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: tab === 'performance' }" @click="tab = 'performance'">
                    Performance <span class="badge" style="background:purple;color:#fff;margin-left:0.25rem">Bulk</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: tab === 'catalog' }" @click="tab = 'catalog'">
                    Catalog <span class="badge" style="background:#0ea5e9;color:#fff;margin-left:0.25rem">12k &middot; Export</span>
                </button>
            </li>
        </ul>
        @endif

        {{-- ═══════════════ PRODUCTS TAB ═══════════════ --}}
        <div x-show="tab === 'products'">
            <div class="lt-section-header">
                <h2 class="lt-section-title">Product Catalog</h2>
                <p class="lt-section-desc">Search, sorting, column visibility, filters, pagination &mdash; plus custom row/column styles.</p>
            </div>
            <div class="lt-stat-grid">
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $totalProducts }}</p>
                    <p class="lt-stat-label">Total Products</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#059669">{{ $activeProducts }}</p>
                    <p class="lt-stat-label">Active</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $categories }}</p>
                    <p class="lt-stat-label">Categories</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#4f46e5">${{ number_format((float) $avgPrice, 0) }}</p>
                    <p class="lt-stat-label">Avg Price</p>
                </div>
            </div>
            <livewire:products-table :table-theme="$theme" :dark-mode="$darkMode" />
        </div>

        {{-- ═══════════════ ORDERS TAB ═══════════════ --}}
        <div x-show="tab === 'orders'" x-cloak>
            <div class="lt-section-header">
                <h2 class="lt-section-title">Orders with Joins</h2>
                <p class="lt-section-desc">Columns and filters using <code style="font-size:.75rem;background:#f3f4f6;padding:.125rem .25rem;border-radius:.25rem">->selectAs()</code> to map joined table fields.</p>
            </div>
            <div class="lt-stat-grid">
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $totalOrders }}</p>
                    <p class="lt-stat-label">Total Orders</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#059669">{{ $deliveredOrders }}</p>
                    <p class="lt-stat-label">Delivered</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $totalBrands }}</p>
                    <p class="lt-stat-label">Brands</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#4f46e5">${{ number_format((float) $ordersRevenue, 0) }}</p>
                    <p class="lt-stat-label">Total Revenue</p>
                </div>
            </div>
            <livewire:orders-table :param="'test'" :table-theme="$theme" :dark-mode="$darkMode" />
        </div>

        {{-- ═══════════════ PERFORMANCE TAB ═══════════════ --}}
        <div x-show="tab === 'performance'" x-cloak>
            <div class="lt-section-header">
                <h2 class="lt-section-title">Performance &amp; Bulk Actions</h2>
                <p class="lt-section-desc">1,000 employee records &mdash; test rendering, filters, and bulk actions.</p>
            </div>
            <div class="lt-stat-grid">
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $totalEmployees }}</p>
                    <p class="lt-stat-label">Total Employees</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#059669">{{ $activeEmployees }}</p>
                    <p class="lt-stat-label">Active</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $departments }}</p>
                    <p class="lt-stat-label">Departments</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#7c3aed">${{ number_format((float) $avgSalary, 0) }}</p>
                    <p class="lt-stat-label">Avg Salary</p>
                </div>
            </div>
            <livewire:performance-table :table-theme="$theme" :dark-mode="$darkMode" />
        </div>

        {{-- ═══════════════ CATALOG TAB ═══════════════ --}}
        <div x-show="tab === 'catalog'" x-cloak>
            <div class="lt-section-header" style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem">
                <div>
                    <h2 class="lt-section-title">Catalog &mdash; 12,000 Items</h2>
                    <p class="lt-section-desc">12,000 product records with images, search, filters, and CSV bulk export.</p>
                </div>
                <livewire:quick-add-catalog-item />
            </div>
            <div class="lt-stat-grid">
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ number_format($totalCatalog) }}</p>
                    <p class="lt-stat-label">Total Items</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#059669">{{ number_format($activeCatalog) }}</p>
                    <p class="lt-stat-label">Active</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value">{{ $catalogCategories }}</p>
                    <p class="lt-stat-label">Categories</p>
                </div>
                <div class="lt-stat-card">
                    <p class="lt-stat-value" style="color:#0284c7">${{ number_format((float) $avgCatalogPrice, 2) }}</p>
                    <p class="lt-stat-label">Avg Price</p>
                </div>
            </div>
            <livewire:catalog-table :table-theme="$theme" :dark-mode="$darkMode" />
        </div>

    </main>
</div>
