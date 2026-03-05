<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\CatalogItem;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Tables\Themes\ThemeManager;

class DemoPage extends Component
{
    public string $theme = 'tailwind';

    public bool $darkMode = false;

    public array $activeFilters = [];

    public array $searchTerms = [];

    #[On('dark-mode-changed')]
    public function onDarkModeChanged(bool $active): void
    {
        $this->darkMode = $active;
    }

    #[On('table-filters-applied')]
    public function onFiltersApplied(string $tableKey, array $filters, string $search = ''): void
    {
        $this->activeFilters[$tableKey] = $filters;
        $this->searchTerms[$tableKey] = $search;
    }

    public function boot(): void
    {
        if ($this->theme !== '') {
            app(ThemeManager::class)->use($this->theme);
        }
    }

    public function mount(string $theme = 'tailwind'): void
    {
        $this->theme = in_array($theme, ['tailwind', 'bootstrap5', 'bootstrap4', 'bootstrap'], true) ? $theme : 'tailwind';
        app(ThemeManager::class)->use($this->theme);
    }

    public function render(): View
    {
        $productsQuery = $this->buildProductsQuery();
        $ordersQuery = $this->buildOrdersQuery();
        $employeesQuery = $this->buildEmployeesQuery();
        $catalogQuery = $this->buildCatalogQuery();

        return view('livewire.demo-page', [
            'theme' => $this->theme,
            'totalProducts' => $productsQuery->count(),
            'activeProducts' => (clone $productsQuery)->where('active', true)->count(),
            'categories' => (clone $productsQuery)->distinct('category')->count('category'),
            'avgPrice' => $productsQuery->avg('price') ?? 0,
            'totalOrders' => $ordersQuery->count(),
            'deliveredOrders' => (clone $ordersQuery)->where('orders.status', 'delivered')->count(),
            'totalBrands' => Brand::count(),
            'ordersRevenue' => (clone $ordersQuery)->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0,
            'totalEmployees' => $employeesQuery->count(),
            'activeEmployees' => (clone $employeesQuery)->where('status', 'active')->count(),
            'departments' => (clone $employeesQuery)->distinct('department')->count('department'),
            'avgSalary' => $employeesQuery->avg('salary') ?? 0,
            'totalCatalog' => $catalogQuery->count(),
            'activeCatalog' => (clone $catalogQuery)->where('active', true)->count(),
            'catalogCategories' => (clone $catalogQuery)->distinct('category')->count('category'),
            'avgCatalogPrice' => $catalogQuery->avg('price') ?? 0,
        ])->extends('layouts.demo', ['theme' => $this->theme])->section('content');
    }

    protected function buildProductsQuery(): Builder
    {
        $query = Product::query();
        $filters = $this->activeFilters['products'] ?? [];
        $search = $this->searchTerms['products'] ?? '';

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%")
                    ->orWhere('category', 'LIKE', "%{$search}%")
                    ->orWhere('subcategory', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }
        if (! empty($filters['cat'])) {
            $query->where('category', $filters['cat']);
        }
        if (! empty($filters['subcat'])) {
            $query->where('subcategory', $filters['subcat']);
        }
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', (bool) $filters['active']);
        }
        if (! empty($filters['price'])) {
            if (($filters['price']['min'] ?? '') !== '') {
                $query->where('price', '>=', (float) $filters['price']['min']);
            }
            if (($filters['price']['max'] ?? '') !== '') {
                $query->where('price', '<=', (float) $filters['price']['max']);
            }
        }
        if (! empty($filters['stock'])) {
            $query->where('stock', '>=', (int) $filters['stock']);
        }
        if (! empty($filters['cats'])) {
            $query->whereIn('category', $filters['cats']);
        }
        if (! empty($filters['release_date'])) {
            $query->whereDate('release_date', $filters['release_date']);
        }
        if (! empty($filters['release_date_range'])) {
            if (($filters['release_date_range']['from'] ?? '') !== '') {
                $query->whereDate('release_date', '>=', $filters['release_date_range']['from']);
            }
            if (($filters['release_date_range']['to'] ?? '') !== '') {
                $query->whereDate('release_date', '<=', $filters['release_date_range']['to']);
            }
        }
        if (! empty($filters['rel_dates'])) {
            $query->whereIn('release_date', $filters['rel_dates']);
        }

        return $query;
    }

    protected function buildOrdersQuery(): Builder
    {
        $query = Order::query()
            ->join('brands', 'brands.id', '=', 'orders.brand_id');
        $filters = $this->activeFilters['orders'] ?? [];
        $search = $this->searchTerms['orders'] ?? '';

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_email', 'LIKE', "%{$search}%")
                    ->orWhere('product_name', 'LIKE', "%{$search}%")
                    ->orWhere('brands.name', 'LIKE', "%{$search}%")
                    ->orWhere('brands.country', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filters['customer'])) {
            $query->where('customer_name', 'LIKE', "%{$filters['customer']}%");
        }
        if (! empty($filters['status'])) {
            $query->where('orders.status', $filters['status']);
        }
        if (! empty($filters['brands_tier'])) {
            $query->where('brands.tier', $filters['brands_tier']);
        }
        if (! empty($filters['brands_country'])) {
            $query->where('brands.country', $filters['brands_country']);
        }
        if (! empty($filters['unit_price'])) {
            if (($filters['unit_price']['min'] ?? '') !== '') {
                $query->where('orders.unit_price', '>=', (float) $filters['unit_price']['min']);
            }
            if (($filters['unit_price']['max'] ?? '') !== '') {
                $query->where('orders.unit_price', '<=', (float) $filters['unit_price']['max']);
            }
        }
        if (! empty($filters['statuses'])) {
            $query->whereIn('orders.status', $filters['statuses']);
        }
        if (! empty($filters['order_dates'])) {
            $query->whereIn('orders.ordered_at', $filters['order_dates']);
        }
        if (! empty($filters['order_range'])) {
            if (($filters['order_range']['from'] ?? '') !== '') {
                $query->whereDate('orders.ordered_at', '>=', $filters['order_range']['from']);
            }
            if (($filters['order_range']['to'] ?? '') !== '') {
                $query->whereDate('orders.ordered_at', '<=', $filters['order_range']['to']);
            }
        }

        return $query;
    }

    protected function buildEmployeesQuery(): Builder
    {
        $query = Employee::query();
        $filters = $this->activeFilters['performance'] ?? [];
        $search = $this->searchTerms['performance'] ?? '';

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('department', 'LIKE', "%{$search}%")
                    ->orWhere('position', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }
        if (! empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['depts'])) {
            $query->whereIn('department', $filters['depts']);
        }
        if (! empty($filters['salary'])) {
            if (($filters['salary']['min'] ?? '') !== '') {
                $query->where('salary', '>=', (float) $filters['salary']['min']);
            }
            if (($filters['salary']['max'] ?? '') !== '') {
                $query->where('salary', '<=', (float) $filters['salary']['max']);
            }
        }

        return $query;
    }

    protected function buildCatalogQuery(): Builder
    {
        $query = CatalogItem::query();
        $filters = $this->activeFilters['catalog'] ?? [];
        $search = $this->searchTerms['catalog'] ?? '';

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('sku', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('brand', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }
        if (! empty($filters['sku'])) {
            $query->where('sku', 'LIKE', "%{$filters['sku']}%");
        }
        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', (bool) $filters['active']);
        }
        if (! empty($filters['price'])) {
            if (($filters['price']['min'] ?? '') !== '') {
                $query->where('price', '>=', (float) $filters['price']['min']);
            }
            if (($filters['price']['max'] ?? '') !== '') {
                $query->where('price', '<=', (float) $filters['price']['max']);
            }
        }
        if (! empty($filters['rating'])) {
            if (($filters['rating']['min'] ?? '') !== '') {
                $query->where('rating', '>=', (float) $filters['rating']['min']);
            }
            if (($filters['rating']['max'] ?? '') !== '') {
                $query->where('rating', '<=', (float) $filters['rating']['max']);
            }
        }

        return $query;
    }
}
