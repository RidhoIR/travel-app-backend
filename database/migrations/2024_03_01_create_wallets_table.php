<?php
// ══════════════════════════════════════════════════════════════
// MIGRATION 1: database/migrations/2024_03_01_add_fields_to_bookings_table.php
// Tambahkan kolom baru ke tabel bookings yang sudah ada
// ══════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['topup', 'payment', 'refund']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('success');
            $table->string('provider')->nullable(); // gopay, ovo, dana, shopeepay
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};