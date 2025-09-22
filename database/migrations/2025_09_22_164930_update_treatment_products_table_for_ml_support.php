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
        Schema::table('treatment_products', function (Blueprint $table) {
            // Change quantity_used to support decimal values for ml-based products
            $table->decimal('quantity_used', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatment_products', function (Blueprint $table) {
            // Revert quantity_used back to integer
            $table->integer('quantity_used')->change();
        });
    }
};
