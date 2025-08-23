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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who made adjustment
            $table->integer('previous_quantity');
            $table->integer('adjusted_quantity');
            $table->integer('difference'); // Can be positive or negative
            $table->string('adjustment_type');
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'created_at']);
            $table->index(['adjustment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
