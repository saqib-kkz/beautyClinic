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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('treatment_id')->constrained()->onDelete('cascade');
            $table->decimal('treatment_amount', 8, 2);
            $table->decimal('vat_percentage', 5, 2)->default(5.00);
            $table->decimal('vat_amount', 8, 2);
            $table->decimal('total_amount', 8, 2);
            $table->string('payment_type');
            $table->string('payment_status');
            $table->date('invoice_date');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['invoice_number']);
            $table->index(['invoice_date', 'payment_status']);
            $table->index(['payment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
