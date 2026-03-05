<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple',            'country' => 'USA',         'tier' => 'premium',  'active' => true],
            ['name' => 'Samsung',          'country' => 'South Korea', 'tier' => 'premium',  'active' => true],
            ['name' => 'Sony',             'country' => 'Japan',       'tier' => 'premium',  'active' => true],
            ['name' => 'Dell',             'country' => 'USA',         'tier' => 'standard', 'active' => true],
            ['name' => 'Logitech',         'country' => 'Switzerland', 'tier' => 'standard', 'active' => true],
            ['name' => 'JBL',              'country' => 'USA',         'tier' => 'standard', 'active' => true],
            ['name' => 'Razer',            'country' => 'Singapore',   'tier' => 'budget',   'active' => true],
            ['name' => 'Nothing',          'country' => 'UK',          'tier' => 'budget',   'active' => false],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }

        $orders = [
            ['brand_id' => 1, 'customer_name' => 'Alice Johnson',   'customer_email' => 'alice@example.com',   'product_name' => 'MacBook Pro 16"',        'quantity' => 1, 'unit_price' => 2499.99, 'status' => 'delivered',  'ordered_at' => '2026-01-05'],
            ['brand_id' => 1, 'customer_name' => 'Bob Martinez',    'customer_email' => 'bob@example.com',     'product_name' => 'iPhone 17 Pro',           'quantity' => 2, 'unit_price' => 1199.00, 'status' => 'delivered',  'ordered_at' => '2026-01-08'],
            ['brand_id' => 1, 'customer_name' => 'Carol White',     'customer_email' => 'carol@example.com',   'product_name' => 'iPad Pro 13"',            'quantity' => 1, 'unit_price' => 1299.00, 'status' => 'shipped',    'ordered_at' => '2026-01-12'],
            ['brand_id' => 1, 'customer_name' => 'David Lee',       'customer_email' => 'david@example.com',   'product_name' => 'AirPods Pro 3',           'quantity' => 3, 'unit_price' => 279.00,  'status' => 'pending',    'ordered_at' => '2026-02-01'],
            ['brand_id' => 1, 'customer_name' => 'Eva Chen',        'customer_email' => 'eva@example.com',     'product_name' => 'Apple Watch Ultra 3',     'quantity' => 1, 'unit_price' => 849.00,  'status' => 'cancelled',  'ordered_at' => '2025-12-20'],
            ['brand_id' => 2, 'customer_name' => 'Frank Torres',    'customer_email' => 'frank@example.com',   'product_name' => 'Samsung Galaxy S26',      'quantity' => 2, 'unit_price' => 999.99,  'status' => 'delivered',  'ordered_at' => '2026-01-15'],
            ['brand_id' => 2, 'customer_name' => 'Grace Kim',       'customer_email' => 'grace@example.com',   'product_name' => 'Samsung Galaxy Tab S10',  'quantity' => 1, 'unit_price' => 849.99,  'status' => 'delivered',  'ordered_at' => '2026-01-18'],
            ['brand_id' => 2, 'customer_name' => 'Henry Nguyen',    'customer_email' => 'henry@example.com',   'product_name' => 'Samsung QN90D 55"',       'quantity' => 1, 'unit_price' => 1299.99, 'status' => 'shipped',    'ordered_at' => '2026-01-22'],
            ['brand_id' => 2, 'customer_name' => 'Irene Patel',     'customer_email' => 'irene@example.com',   'product_name' => 'Samsung Galaxy Watch 7',  'quantity' => 2, 'unit_price' => 349.99,  'status' => 'pending',    'ordered_at' => '2026-02-05'],
            ['brand_id' => 3, 'customer_name' => 'James Russo',     'customer_email' => 'james@example.com',   'product_name' => 'Sony WH-1000XM6',         'quantity' => 2, 'unit_price' => 399.99,  'status' => 'delivered',  'ordered_at' => '2026-01-03'],
            ['brand_id' => 3, 'customer_name' => 'Karen Schmidt',   'customer_email' => 'karen@example.com',   'product_name' => 'Sony Bravia A95L 65"',    'quantity' => 1, 'unit_price' => 2799.99, 'status' => 'delivered',  'ordered_at' => '2026-01-10'],
            ['brand_id' => 3, 'customer_name' => 'Luis Fernandez',  'customer_email' => 'luis@example.com',    'product_name' => 'Sony Bose QC Ultra',      'quantity' => 1, 'unit_price' => 429.00,  'status' => 'cancelled',  'ordered_at' => '2025-12-15'],
            ['brand_id' => 4, 'customer_name' => 'Maria Gonzalez',  'customer_email' => 'maria@example.com',   'product_name' => 'Dell XPS 15',             'quantity' => 1, 'unit_price' => 1799.99, 'status' => 'shipped',    'ordered_at' => '2026-01-19'],
            ['brand_id' => 4, 'customer_name' => 'Nathan Brown',    'customer_email' => 'nathan@example.com',  'product_name' => 'Dell XPS 13 Plus',        'quantity' => 2, 'unit_price' => 1299.99, 'status' => 'delivered',  'ordered_at' => '2026-01-06'],
            ['brand_id' => 4, 'customer_name' => 'Olivia Park',     'customer_email' => 'olivia@example.com',  'product_name' => 'Dell UltraSharp 27"',     'quantity' => 1, 'unit_price' => 699.99,  'status' => 'pending',    'ordered_at' => '2026-02-10'],
            ['brand_id' => 5, 'customer_name' => 'Peter Quinn',     'customer_email' => 'peter@example.com',   'product_name' => 'Logitech MX Master 4',    'quantity' => 5, 'unit_price' => 109.99,  'status' => 'delivered',  'ordered_at' => '2026-01-07'],
            ['brand_id' => 5, 'customer_name' => 'Quinn Adams',     'customer_email' => 'quinn@example.com',   'product_name' => 'Logitech MX Keys S',      'quantity' => 3, 'unit_price' => 119.99,  'status' => 'delivered',  'ordered_at' => '2026-01-14'],
            ['brand_id' => 5, 'customer_name' => 'Rachel Moore',    'customer_email' => 'rachel@example.com',  'product_name' => 'Logitech StreamCam',      'quantity' => 2, 'unit_price' => 169.99,  'status' => 'shipped',    'ordered_at' => '2026-01-25'],
            ['brand_id' => 5, 'customer_name' => 'Samuel Davis',    'customer_email' => 'samuel@example.com',  'product_name' => 'Logitech G Pro X 2',      'quantity' => 1, 'unit_price' => 159.99,  'status' => 'pending',    'ordered_at' => '2026-02-08'],
            ['brand_id' => 6, 'customer_name' => 'Tina Wilson',     'customer_email' => 'tina@example.com',    'product_name' => 'JBL Charge 6',            'quantity' => 4, 'unit_price' => 179.99,  'status' => 'delivered',  'ordered_at' => '2026-01-02'],
            ['brand_id' => 6, 'customer_name' => 'Uma Patel',       'customer_email' => 'uma@example.com',     'product_name' => 'JBL Flip 7',              'quantity' => 2, 'unit_price' => 129.99,  'status' => 'shipped',    'ordered_at' => '2026-01-20'],
            ['brand_id' => 6, 'customer_name' => 'Victor Zhang',    'customer_email' => 'victor@example.com',  'product_name' => 'JBL Quantum 910',         'quantity' => 1, 'unit_price' => 199.99,  'status' => 'cancelled',  'ordered_at' => '2025-12-28'],
            ['brand_id' => 7, 'customer_name' => 'Wendy Clark',     'customer_email' => 'wendy@example.com',   'product_name' => 'Razer Blade 18',          'quantity' => 1, 'unit_price' => 3499.99, 'status' => 'delivered',  'ordered_at' => '2026-01-09'],
            ['brand_id' => 7, 'customer_name' => 'Xander Hall',     'customer_email' => 'xander@example.com',  'product_name' => 'Razer BlackWidow V4',     'quantity' => 2, 'unit_price' => 189.99,  'status' => 'pending',    'ordered_at' => '2026-02-03'],
            ['brand_id' => 7, 'customer_name' => 'Yara Lopez',      'customer_email' => 'yara@example.com',    'product_name' => 'Razer DeathAdder V3',     'quantity' => 3, 'unit_price' => 79.99,   'status' => 'shipped',    'ordered_at' => '2026-01-27'],
            ['brand_id' => 7, 'customer_name' => 'Zoe Evans',       'customer_email' => 'zoe@example.com',     'product_name' => 'Razer Kraken V4',         'quantity' => 1, 'unit_price' => 129.99,  'status' => 'delivered',  'ordered_at' => '2026-01-16'],
            ['brand_id' => 8, 'customer_name' => 'Aaron Hill',      'customer_email' => 'aaron@example.com',   'product_name' => 'Nothing Phone 3',         'quantity' => 2, 'unit_price' => 599.00,  'status' => 'delivered',  'ordered_at' => '2026-01-11'],
            ['brand_id' => 8, 'customer_name' => 'Bella Turner',    'customer_email' => 'bella@example.com',   'product_name' => 'Nothing Ear 2',           'quantity' => 3, 'unit_price' => 149.00,  'status' => 'shipped',    'ordered_at' => '2026-01-24'],
            ['brand_id' => 8, 'customer_name' => 'Carlos Rivera',   'customer_email' => 'carlos@example.com',  'product_name' => 'Nothing CMF Phone 2',     'quantity' => 1, 'unit_price' => 279.00,  'status' => 'pending',    'ordered_at' => '2026-02-12'],
            ['brand_id' => 1, 'customer_name' => 'Diana Foster',    'customer_email' => 'diana@example.com',   'product_name' => 'MacBook Air 15"',         'quantity' => 1, 'unit_price' => 1299.00, 'status' => 'shipped',    'ordered_at' => '2026-01-30'],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
