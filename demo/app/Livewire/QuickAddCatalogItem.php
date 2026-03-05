<?php

namespace App\Livewire;

use App\Models\CatalogItem;
use Livewire\Component;

class QuickAddCatalogItem extends Component
{
    public bool $open = false;

    public string $name = '';

    public string $sku = '';

    public string $category = 'Electronics';

    public string $brand = '';

    public string $country = 'USA';

    public float $price = 0.0;

    public int $stock = 0;

    /** @var array<string, string> */
    protected array $rules = [
        'name' => 'required|string|min:2|max:255',
        'sku' => 'required|string|max:32|unique:catalog_items,sku',
        'category' => 'required|string',
        'brand' => 'required|string|max:255',
        'country' => 'required|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
    ];

    public function categories(): array
    {
        return [
            'Electronics',
            'Clothing',
            'Food & Drinks',
            'Home & Garden',
            'Sports',
            'Books',
            'Toys & Games',
            'Beauty',
            'Automotive',
            'Pet Supplies',
        ];
    }

    public function save(): void
    {
        $this->validate();

        CatalogItem::create([
            'name' => $this->name,
            'sku' => strtoupper($this->sku),
            'category' => $this->category,
            'brand' => $this->brand,
            'country' => $this->country,
            'price' => $this->price,
            'stock' => $this->stock,
            'rating' => 0.0,
            'active' => true,
            'image_url' => 'https://picsum.photos/seed/'.strtoupper($this->sku).'/80/80',
        ]);

        $this->reset(['name', 'sku', 'brand', 'price', 'stock']);
        $this->open = false;

        $this->dispatch('catalog-refresh');
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.quick-add-catalog-item');
    }
}
