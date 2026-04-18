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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};