<?php
// database/migrations/2024_01_01_000003_create_bookings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('nights')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('confirmed'); // confirmed, cancelled, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
