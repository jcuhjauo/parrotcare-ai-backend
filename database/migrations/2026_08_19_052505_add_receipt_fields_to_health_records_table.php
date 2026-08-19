<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->string('clinic_name')->nullable()->after('parrot_id');
            $table->string('clinic_phone')->nullable()->after('clinic_name');
            $table->text('clinic_address')->nullable()->after('clinic_phone');
            $table->string('owner_name')->nullable()->after('clinic_address');
            $table->string('owner_phone')->nullable()->after('owner_name');
            $table->string('pet_name')->nullable()->after('owner_phone');
            $table->string('species')->nullable()->after('pet_name');
            $table->json('line_items')->nullable()->after('medications');
            $table->float('total_amount')->nullable()->after('line_items');
        });
    }

    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropColumn([
                'clinic_name', 'clinic_phone', 'clinic_address',
                'owner_name', 'owner_phone', 'pet_name', 'species',
                'line_items', 'total_amount',
            ]);
        });
    }
};