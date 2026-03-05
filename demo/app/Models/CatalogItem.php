<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'brand',
        'price',
        'stock',
        'rating',
        'country',
        'active',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'active' => 'boolean',
        'stock' => 'integer',
    ];
}
