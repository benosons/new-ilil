<?php

namespace App\Traits;

use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Auth;

trait LoggableTransaction
{
    /**
     * Boot the trait for a model.
     * Hooks into the model's Eloquent events.
     */
    public static function bootLoggableTransaction()
    {
        // When the model is created
        static::created(function ($model) {
            $model->logTransactionHistory(null, $model->status, 'Pesanan baru dibuat');
        });

        // When the model is updated
        static::updated(function ($model) {
            if ($model->isDirty('status')) {
                $statusFrom = $model->getOriginal('status');
                $statusTo = $model->status;
                
                $model->logTransactionHistory($statusFrom, $statusTo, 'Status diperbarui');
            }
        });
    }

    /**
     * Relationship to retrieve the history logs.
     */
    public function transactionHistories()
    {
        return $this->morphMany(TransactionHistory::class, 'orderable', 'order_type', 'order_id');
    }

    /**
     * Log a row in the transaction history table.
     */
    public function logTransactionHistory($statusFrom, $statusTo, $notes = null)
    {
        $changedBy = 'system';
        if (Auth::check()) {
            $changedBy = Auth::user()->name . ' (Admin)';
        }

        // Handle dynamically finding order number column (depends if Order or StandaloneOrder)
        $orderNumber = $this->order_number ?? $this->wa_number ?? null;

        TransactionHistory::create([
            'order_type' => get_class($this),
            'order_id' => $this->id,
            'order_number' => $orderNumber,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'notes' => $notes,
            'changed_by' => $changedBy,
        ]);
    }
}
