<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'key', 'name', 'description', 'price', 'image_path',
        'glow_color', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /* --- Relations --- */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /* --- Scopes --- */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /* --- Accessors --- */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? asset($this->image_path) : asset('assets/brand/logo.png');
    }
}
