<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'subcategory',
        'sku',
        'description',
        'image_url',
        'price',
        'stock',
        'active',
        'release_date',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'active' => 'boolean',
            'release_date' => 'date',
        ];
    }
}
