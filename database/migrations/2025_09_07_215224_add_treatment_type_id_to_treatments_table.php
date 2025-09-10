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
            $table->foreignId('treatment_type_id')->nullable()->after('user_id')->constrained('treatment_types');
            $table->index('treatment_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropForeign(['treatment_type_id']);
            $table->dropIndex(['treatment_type_id']);
            $table->dropColumn('treatment_type_id');
        });
    }
};
