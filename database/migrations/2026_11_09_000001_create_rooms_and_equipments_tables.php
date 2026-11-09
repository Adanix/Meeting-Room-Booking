<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_en', 100);
            $table->string('name_sr', 100);
            $table->string('location', 100);
            $table->unsignedSmallInteger('capacity');
            $table->text('description_en')->nullable();
            $table->text('description_sr')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('color_hex', 7)->default('#4F46E5');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('room_equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('label_en', 100);
            $table->string('label_sr', 100);
            $table->timestamps();

            $table->index(['room_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_equipments');
        Schema::dropIfExists('rooms');
    }
};
