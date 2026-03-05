<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'MacBook Pro 16"', 'category' => 'Laptops', 'subcategory' => 'Business Laptops', 'sku' => 'LAP-001', 'description' => 'Apple M4 Pro chip, 18GB RAM, 512GB SSD, Liquid Retina XDR display', 'image_url' => 'https://picsum.photos/seed/macbook/400/300', 'price' => 2499.99, 'stock' => 25, 'active' => true, 'release_date' => '2025-10-15'],
            ['name' => 'Dell XPS 15', 'category' => 'Laptops', 'subcategory' => 'Business Laptops', 'sku' => 'LAP-002', 'description' => 'Intel Core Ultra 7, 16GB RAM, OLED display, Thunderbolt 4', 'image_url' => 'https://picsum.photos/seed/dellxps/400/300', 'price' => 1799.99, 'stock' => 40, 'active' => true, 'release_date' => '2025-08-20'],
            ['name' => 'ThinkPad X1 Carbon', 'category' => 'Laptops', 'subcategory' => 'Ultrabooks', 'sku' => 'LAP-003', 'description' => 'Ultra-lightweight business laptop, 14" 2.8K OLED, 32GB RAM', 'image_url' => 'https://picsum.photos/seed/thinkpad/400/300', 'price' => 1649.00, 'stock' => 30, 'active' => true, 'release_date' => '2025-06-10'],
            ['name' => 'Razer Blade 18', 'category' => 'Laptops', 'subcategory' => 'Gaming Laptops', 'sku' => 'LAP-004', 'description' => 'Gaming laptop, RTX 5080, 18" QHD+ 240Hz, 32GB DDR5', 'image_url' => 'https://picsum.photos/seed/razer/400/300', 'price' => 3499.99, 'stock' => 0, 'active' => false, 'release_date' => '2024-06-01'],
            ['name' => 'iPhone 17 Pro', 'category' => 'Phones', 'subcategory' => 'Flagship', 'sku' => 'PHN-001', 'description' => 'A19 Pro chip, titanium design, 48MP camera system, USB-C', 'image_url' => 'https://picsum.photos/seed/iphone/400/300', 'price' => 1199.00, 'stock' => 150, 'active' => true, 'release_date' => '2025-09-22'],
            ['name' => 'Samsung Galaxy S26', 'category' => 'Phones', 'subcategory' => 'Flagship', 'sku' => 'PHN-002', 'description' => 'Galaxy AI, Snapdragon 8 Elite, 200MP camera, One UI 8', 'image_url' => 'https://picsum.photos/seed/galaxy/400/300', 'price' => 999.99, 'stock' => 120, 'active' => true, 'release_date' => '2026-01-15'],
            ['name' => 'Google Pixel 10', 'category' => 'Phones', 'subcategory' => 'Flagship', 'sku' => 'PHN-003', 'description' => 'Tensor G5, pure Android experience, exceptional AI camera', 'image_url' => 'https://picsum.photos/seed/pixel/400/300', 'price' => 899.00, 'stock' => 80, 'active' => true, 'release_date' => '2025-10-05'],
            ['name' => 'Nothing Phone 3', 'category' => 'Phones', 'subcategory' => 'Mid-range', 'sku' => 'PHN-004', 'description' => 'Glyph interface 2.0, transparent design, SD 8s Gen 4', 'image_url' => 'https://picsum.photos/seed/nothing/400/300', 'price' => 599.00, 'stock' => 65, 'active' => true, 'release_date' => '2026-02-10'],
            ['name' => 'iPad Pro 13"', 'category' => 'Tablets', 'subcategory' => 'iPad', 'sku' => 'TAB-001', 'description' => 'M4 chip, Ultra Retina XDR, Apple Pencil Pro support', 'image_url' => 'https://picsum.photos/seed/ipad/400/300', 'price' => 1299.00, 'stock' => 60, 'active' => true, 'release_date' => '2025-05-12'],
            ['name' => 'Samsung Galaxy Tab S10', 'category' => 'Tablets', 'subcategory' => 'Android Tablets', 'sku' => 'TAB-002', 'description' => 'AMOLED 2X display, S Pen included, DeX mode', 'image_url' => 'https://picsum.photos/seed/tabs10/400/300', 'price' => 849.99, 'stock' => 45, 'active' => true, 'release_date' => '2025-07-18'],
            ['name' => 'Kindle Scribe 2', 'category' => 'Tablets', 'subcategory' => 'E-readers', 'sku' => 'TAB-003', 'description' => 'E-ink writing tablet, 10.2" Paperwhite display, stylus', 'image_url' => 'https://picsum.photos/seed/kindle/400/300', 'price' => 399.99, 'stock' => 40, 'active' => true, 'release_date' => '2025-10-30'],
            ['name' => 'Sony WH-1000XM6', 'category' => 'Audio', 'subcategory' => 'Headphones', 'sku' => 'AUD-001', 'description' => 'Industry-leading noise cancellation, 40h battery, LDAC', 'image_url' => 'https://picsum.photos/seed/sonyxm6/400/300', 'price' => 399.99, 'stock' => 200, 'active' => true, 'release_date' => '2025-04-08'],
            ['name' => 'AirPods Pro 3', 'category' => 'Audio', 'subcategory' => 'Earbuds', 'sku' => 'AUD-002', 'description' => 'H3 chip, adaptive audio, conversation awareness, USB-C', 'image_url' => 'https://picsum.photos/seed/airpods/400/300', 'price' => 279.00, 'stock' => 300, 'active' => true, 'release_date' => '2025-09-22'],
            ['name' => 'Bose QC Ultra', 'category' => 'Audio', 'subcategory' => 'Headphones', 'sku' => 'AUD-003', 'description' => 'Spatial audio, CustomTune technology, Snapdragon Sound', 'image_url' => 'https://picsum.photos/seed/bose/400/300', 'price' => 429.00, 'stock' => 90, 'active' => false, 'release_date' => '2024-11-20'],
            ['name' => 'JBL Charge 6', 'category' => 'Audio', 'subcategory' => 'Speakers', 'sku' => 'AUD-004', 'description' => 'Portable Bluetooth speaker, IP67, 24h playtime', 'image_url' => 'https://picsum.photos/seed/jbl/400/300', 'price' => 179.99, 'stock' => 220, 'active' => true, 'release_date' => '2025-05-20'],
            ['name' => 'Apple Watch Ultra 3', 'category' => 'Wearables', 'subcategory' => 'Smartwatches', 'sku' => 'WRB-001', 'description' => 'Titanium, dual-frequency GPS, 72h battery, dive computer', 'image_url' => 'https://picsum.photos/seed/awultra/400/300', 'price' => 849.00, 'stock' => 55, 'active' => true, 'release_date' => '2025-09-22'],
            ['name' => 'Samsung Galaxy Watch 7', 'category' => 'Wearables', 'subcategory' => 'Smartwatches', 'sku' => 'WRB-002', 'description' => 'BioActive sensor, Wear OS 5, sapphire crystal', 'image_url' => 'https://picsum.photos/seed/gwatch/400/300', 'price' => 349.99, 'stock' => 70, 'active' => true, 'release_date' => '2025-08-05'],
            ['name' => 'Meta Quest 4', 'category' => 'Wearables', 'subcategory' => 'VR Headsets', 'sku' => 'WRB-003', 'description' => 'Mixed reality headset, Snapdragon XR3, 4K per eye', 'image_url' => 'https://picsum.photos/seed/quest/400/300', 'price' => 499.99, 'stock' => 85, 'active' => true, 'release_date' => '2025-12-15'],
            ['name' => 'ASUS ROG Ally 2', 'category' => 'Wearables', 'subcategory' => 'Gaming Handhelds', 'sku' => 'WRB-004', 'description' => 'Handheld gaming PC, Ryzen Z2 Extreme, 120Hz OLED', 'image_url' => 'https://picsum.photos/seed/rogally/400/300', 'price' => 699.99, 'stock' => 30, 'active' => true, 'release_date' => '2025-07-22'],
            ['name' => 'LG OLED C4 65"', 'category' => 'TVs', 'subcategory' => 'OLED TVs', 'sku' => 'TV-001', 'description' => 'OLED evo, a9 AI processor, 4K 120Hz, Dolby Vision IQ', 'image_url' => 'https://picsum.photos/seed/lgc4/400/300', 'price' => 1799.99, 'stock' => 15, 'active' => true, 'release_date' => '2025-03-10'],
            ['name' => 'Sony Bravia A95L', 'category' => 'TVs', 'subcategory' => 'OLED TVs', 'sku' => 'TV-002', 'description' => 'QD-OLED, Cognitive Processor XR, Acoustic Surface Audio+', 'image_url' => 'https://picsum.photos/seed/bravia/400/300', 'price' => 2799.99, 'stock' => 10, 'active' => true, 'release_date' => '2025-06-25'],
            ['name' => 'Samsung QN90D 55"', 'category' => 'TVs', 'subcategory' => 'QLED TVs', 'sku' => 'TV-003', 'description' => 'Neo QLED, NQ4 AI processor, anti-reflection, 4K 144Hz', 'image_url' => 'https://picsum.photos/seed/qn90d/400/300', 'price' => 1299.99, 'stock' => 20, 'active' => false, 'release_date' => '2024-12-01'],
            ['name' => 'Logitech MX Master 4', 'category' => 'Accessories', 'subcategory' => 'Peripherals', 'sku' => 'ACC-001', 'description' => 'Ergonomic wireless mouse, MagSpeed scroll, USB-C, multi-device', 'image_url' => 'https://picsum.photos/seed/mxmaster/400/300', 'price' => 109.99, 'stock' => 500, 'active' => true, 'release_date' => '2025-11-10'],
            ['name' => 'Keychron Q1 Pro', 'category' => 'Accessories', 'subcategory' => 'Peripherals', 'sku' => 'ACC-002', 'description' => 'Mechanical keyboard, wireless, hot-swappable, QMK/VIA', 'image_url' => 'https://picsum.photos/seed/keychron/400/300', 'price' => 199.00, 'stock' => 150, 'active' => true, 'release_date' => '2025-02-28'],
            ['name' => 'CalDigit TS4', 'category' => 'Accessories', 'subcategory' => 'Docking Stations', 'sku' => 'ACC-003', 'description' => 'Thunderbolt 4 dock, 18 ports, 98W charging, 2.5GbE', 'image_url' => 'https://picsum.photos/seed/caldigit/400/300', 'price' => 449.99, 'stock' => 35, 'active' => true, 'release_date' => '2024-08-15'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
