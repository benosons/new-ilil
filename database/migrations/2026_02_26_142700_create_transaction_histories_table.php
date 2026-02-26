<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relation to orders
            $table->string('order_type');
            $table->unsignedBigInteger('order_id');
            
            $table->string('order_number')->nullable()->index();
            $table->string('status_from')->nullable();
            $table->string('status_to');
            $table->text('notes')->nullable();
            
            // e.g., 'User #1', 'System'
            $table->string('changed_by')->default('system');

            $table->timestamps();

            // Index covering the polymorphism
            $table->index(['order_type', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_histories');
    }
};
