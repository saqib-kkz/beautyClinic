<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clinic_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name');
            $table->string('logo_path')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('license_number')->nullable();
            $table->timestamps();
        });

        // Insert default clinic data
        DB::table('clinic_profiles')->insert([
            'clinic_name' => 'Swan Aesthetic Clinic',
            'address' => 'Your clinic address here',
            'phone' => '+971 XX XXX XXXX',
            'email' => 'info@swanaesthetic.com',
            'website' => 'www.swanaesthetic.com',
            'description' => 'Professional aesthetic and beauty treatments',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_profiles');
    }
};
