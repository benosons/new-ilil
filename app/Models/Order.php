<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_name', 'customer_phone', 'customer_email',
        'customer_address', 'subtotal', 'shipping_cost', 'discount_amount', 'total', 'status',
        'payment_method', 'voucher_code', 'midtrans_snap_token', 'midtrans_transaction_id',
        'paid_at', 'notes', 'tracking_number', 'shipping_courier', 'shipped_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'shipping_cost' => 'integer',
        'total' => 'integer',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];

    /* --- Relations --- */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /* --- Helpers --- */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ILIL';
        $date = now()->format('ymd');
        $rand = strtoupper(substr(uniqid(), -4));
        return "{$prefix}-{$date}-{$rand}";
    }

    /* --- Accessors --- */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '<span class="badge-status pending">Pending</span>',
            'paid'       => '<span class="badge-status paid">Dibayar</span>',
            'processing' => '<span class="badge-status processing">Diproses</span>',
            'shipped'    => '<span class="badge-status shipped">Dikirim</span>',
            'completed'  => '<span class="badge-status completed">Selesai</span>',
            'cancelled'  => '<span class="badge-status cancelled">Batal</span>',
            default      => '<span class="badge-status">' . $this->status . '</span>',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '#ffd54a',
            'paid'       => '#39d98a',
            'processing' => '#5b8def',
            'shipped'    => '#a855f7',
            'completed'  => '#22c55e',
            'cancelled'  => '#ff3b5c',
            default      => '#888',
        };
    }
}
