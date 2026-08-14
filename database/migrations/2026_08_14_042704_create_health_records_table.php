<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parrot_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date')->nullable();
            $table->float('weight_grams')->nullable();
            $table->json('medications')->nullable();
            $table->date('next_visit_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('ai_confidence', 10)->nullable();
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};