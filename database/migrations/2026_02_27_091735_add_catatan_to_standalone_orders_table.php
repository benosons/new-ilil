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
        if (!Schema::hasColumn('standalone_orders', 'catatan')) {
            Schema::table('standalone_orders', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standalone_orders', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
