<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('time_slot');
            $table->integer('quota')->default(20);
            $table->integer('booked')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->index(['destination_id', 'date']);
        });
    }
    public function down(): void { Schema::dropIfExists('schedules'); }
};