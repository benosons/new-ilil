<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandaloneOrder extends Model
{
    use \App\Traits\LoggableTransaction;

    protected $fillable = [
        'name',
        'wa_number',
        'email',
        'total_amount',
        'status',
        'voucher_code',
        'discount_amount'
    ];

    protected $casts = [
        'total_price' => 'integer',
    ];

    /* --- Relations --- */
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StandaloneOrderItem::class);
    }

    /* --- Accessors --- */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '<span class="badge-status pending" style="background: rgba(255,213,74,.15); color: #ffd54a;">Pending</span>',
            'processed'  => '<span class="badge-status processing" style="background: rgba(91,141,239,.15); color: #5b8def;">Diproses</span>',
            'completed'  => '<span class="badge-status completed" style="background: rgba(34,197,94,.15); color: #22c55e;">Selesai</span>',
            'cancelled'  => '<span class="badge-status cancelled" style="background: rgba(255,59,92,.15); color: #ff3b5c;">Batal</span>',
            default      => '<span class="badge-status">' . $this->status . '</span>',
        };
    }
}
