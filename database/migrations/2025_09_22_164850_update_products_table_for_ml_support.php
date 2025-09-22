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
        Schema::table('products', function (Blueprint $table) {
            // Change stock_quantity to support decimal values for ml-based products
            $table->decimal('stock_quantity', 10, 2)->default(0.00)->change();
            $table->decimal('low_stock_threshold', 10, 2)->default(5.00)->change();

            // Add a column to store price per ml for vials
            $table->decimal('price_per_ml', 8, 2)->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert stock_quantity and low_stock_threshold back to integer
            $table->integer('stock_quantity')->default(0)->change();
            $table->integer('low_stock_threshold')->default(5)->change();

            // Drop the price_per_ml column
            $table->dropColumn('price_per_ml');
        });
    }
};
