<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'brand_id',
        'customer_name',
        'customer_email',
        'product_name',
        'quantity',
        'unit_price',
        'status',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'ordered_at' => 'date',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
