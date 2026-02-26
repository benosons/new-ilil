<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $fillable = [
        'order_type',     // App\Models\Order or App\Models\StandaloneOrder
        'order_id',       // ID of the order
        'order_number',   // Reference number (e.g., ORD-xxx, or WA phone number)
        'status_from',    // Previous status
        'status_to',      // New status
        'notes',          // Any extra context
        'changed_by'      // User ID or 'system'
    ];

    /**
     * Get the parent order model (Order or StandaloneOrder).
     */
    public function orderable()
    {
        return $this->morphTo(null, 'order_type', 'order_id');
    }
}
