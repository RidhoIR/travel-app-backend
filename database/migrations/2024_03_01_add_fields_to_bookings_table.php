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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->string('payment_method')->default('wallet')->after('payment_status');
            $table->integer('guests')->default(1)->after('payment_method');
            $table->foreignId('schedule_id')->nullable()->after('guests')
                  ->constrained()->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_method', 'guests', 'schedule_id']);
        });
    }
};