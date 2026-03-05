<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
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

        $brands = [
            'Apple',
            'Samsung',
            'Sony',
            'LG',
            'Nike',
            'Adidas',
            'Zara',
            'Uniqlo',
            'Philips',
            'Bosch',
            'Panasonic',
            'Canon',
            'Dell',
            'HP',
            'Lenovo',
            'ASUS',
            'Logitech',
            'Jabra',
            'Bose',
            'JBL',
            'Dyson',
            'KitchenAid',
            'Cuisinart',
            'Nespresso',
            'IKEA',
            'Casper',
            'Levi\'s',
            'Gap',
            'Calvin Klein',
            'Nestlé',
            'Heinz',
            'Kellogg\'s',
            'Procter & Gamble',
            'Unilever',
            'L\'Oréal',
            'Colgate',
            'Razer',
            'Microsoft',
            'Acer',
            'Beats',
            'Instant Pot',
            'Herman Miller',
            'Purple',
            'Tommy Hilfiger',
            'Pepsi',
            'Gillette',
            'Amazon Basics',
            'Tempur',
            'Hasbro',
            'Mattel',
        ];

        $countries = [
            'USA',
            'Germany',
            'Japan',
            'China',
            'France',
            'Italy',
            'Spain',
            'UK',
            'Canada',
            'Australia',
            'Brazil',
            'Mexico',
            'South Korea',
            'India',
            'Netherlands',
            'Sweden',
            'Switzerland',
            'Denmark',
            'Poland',
            'Portugal',
        ];

        $productWords = [
            'Pro',
            'Max',
            'Ultra',
            'Mini',
            'Slim',
            'Elite',
            'Plus',
            'Air',
            'Neo',
            'Lite',
            'Smart',
            'Turbo',
            'Flex',
            'Edge',
            'Core',
            'Prime',
            'Swift',
            'Boost',
            'Pulse',
            'Vibe',
            'Wave',
            'Zen',
            'Peak',
            'Bold',
            'Pure',
        ];

        $productTypes = [
            'Electronics' => ['Laptop', 'Smartphone', 'Tablet', 'Monitor', 'Headphones', 'Speaker', 'Camera', 'Keyboard', 'Mouse', 'Charger'],
            'Clothing' => ['T-Shirt', 'Jeans', 'Jacket', 'Sneakers', 'Dress', 'Hoodie', 'Shorts', 'Socks', 'Cap', 'Backpack'],
            'Food & Drinks' => ['Coffee Beans', 'Protein Bar', 'Olive Oil', 'Tea Pack', 'Energy Drink', 'Yogurt', 'Granola', 'Hot Sauce', 'Juice', 'Chips'],
            'Home & Garden' => ['Lamp', 'Pillow', 'Rug', 'Plant Pot', 'Candle', 'Mirror', 'Shelf', 'Curtain', 'Towel Set', 'Organizer'],
            'Sports' => ['Yoga Mat', 'Dumbbell', 'Resistance Band', 'Water Bottle', 'Running Shoes', 'Gym Bag', 'Cycling Gloves', 'Jump Rope', 'Foam Roller', 'Knee Pad'],
            'Books' => ['Novel', 'Biography', 'Cookbook', 'Self-Help Guide', 'Science Book', 'Art Book', 'Technical Manual', 'History Book', 'Poetry Collection', 'Travel Guide'],
            'Toys & Games' => ['Board Game', 'Action Figure', 'Puzzle', 'Building Blocks', 'Remote Car', 'Stuffed Animal', 'Card Game', 'Drone Kit', 'Art Set', 'Robot Kit'],
            'Beauty' => ['Face Cream', 'Shampoo', 'Perfume', 'Lipstick', 'Serum', 'Sunscreen', 'Face Mask', 'Body Lotion', 'Eye Shadow', 'Foundation'],
            'Automotive' => ['Car Charger', 'Seat Cover', 'Dashboard Camera', 'Air Freshener', 'Tire Inflator', 'Car Wax', 'Phone Holder', 'Jump Starter', 'Floor Mats', 'LED Lights'],
            'Pet Supplies' => ['Dog Food', 'Cat Toy', 'Pet Bed', 'Grooming Brush', 'Bird Cage', 'Fish Tank', 'Chew Toy', 'Pet Carrier', 'Collar', 'Training Treats'],
        ];

        $priceRanges = [
            'Electronics' => [29.99, 1499.99],
            'Clothing' => [9.99, 299.99],
            'Food & Drinks' => [2.99, 59.99],
            'Home & Garden' => [4.99, 399.99],
            'Sports' => [7.99, 249.99],
            'Books' => [5.99, 89.99],
            'Toys & Games' => [9.99, 179.99],
            'Beauty' => [3.99, 149.99],
            'Automotive' => [9.99, 299.99],
            'Pet Supplies' => [4.99, 119.99],
        ];

        $now = now();
        $rows = [];

        for ($i = 1; $i <= 12000; $i++) {
            $category = $categories[$i % count($categories)];
            $brand = $brands[($i * 7 + 3) % count($brands)];
            $country = $countries[($i * 11 + 7) % count($countries)];
            $types = $productTypes[$category];
            $type = $types[$i % count($types)];
            $word = $productWords[($i * 13 + 5) % count($productWords)];
            $name = $brand.' '.$type.' '.$word;
            $sku = strtoupper(substr($category, 0, 3)).'-'.str_pad((string) $i, 6, '0', STR_PAD_LEFT);

            [$minP, $maxP] = $priceRanges[$category];
            $range = $maxP - $minP;
            $price = round($minP + (($i * 97 + 41) % (int) ($range * 100)) / 100, 2);
            $stock = ($i * 31 + 17) % 500;
            $rating = round(1.0 + (($i * 23 + 11) % 40) / 10, 1);
            $active = ($i % 7) !== 0;
            $image = 'https://picsum.photos/seed/'.$sku.'/80/80';

            $rows[] = [
                'name' => $name,
                'sku' => $sku,
                'description' => $type.' from '.$brand.' — '.$word.' edition, shipped from '.$country.'.',
                'category' => $category,
                'brand' => $brand,
                'price' => $price,
                'stock' => $stock,
                'rating' => $rating,
                'country' => $country,
                'active' => $active,
                'image_url' => $image,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) === 200) {
                DB::table('catalog_items')->insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            DB::table('catalog_items')->insert($rows);
        }
    }
}
