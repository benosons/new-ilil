<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandaloneOrderItem extends Model
{
    protected $fillable = [
        'standalone_order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function standaloneOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StandaloneOrder::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
