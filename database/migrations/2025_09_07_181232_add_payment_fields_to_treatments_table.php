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
        Schema::table('treatments', function (Blueprint $table) {
            $table->decimal('treatment_amount', 10, 2)->default(0)->after('notes');
            $table->decimal('vat_amount', 10, 2)->default(0)->after('treatment_amount');
            $table->decimal('discount', 10, 2)->default(0)->after('vat_amount');
            $table->decimal('total_amount_received', 10, 2)->default(0)->after('discount');
            $table->enum('payment_type', ['cash', 'card', 'tabby', 'tamara', 'bank_transfer'])->nullable()->after('total_amount_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropColumn(['treatment_amount', 'vat_amount', 'discount', 'total_amount_received', 'payment_type']);
        });
    }
};
