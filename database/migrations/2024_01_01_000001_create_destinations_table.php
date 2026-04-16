<?php
// database/migrations/2024_01_01_000001_create_destinations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('country');
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2)->default(0);
            $table->decimal('rating', 3, 1)->default(0);
            $table->decimal('distance_km', 6, 1)->default(0);
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_pool')->default(false);
            $table->boolean('has_restaurant')->default(false);
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_spa')->default(false);
            $table->string('image_url')->nullable();
            $table->string('category')->default('hotel'); // hotel, beach, resort, city
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
