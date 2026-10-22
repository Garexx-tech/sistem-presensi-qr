<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto_wajah')->nullable()->after('parent_phone')
                ->comment('Penanda wajah terdaftar (berisi NIS siswa)');
        });

        DB::statement("ALTER TABLE presensis MODIFY COLUMN metode ENUM('qr','manual','wajah') NOT NULL DEFAULT 'manual'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE presensis MODIFY COLUMN metode ENUM('qr','manual') NOT NULL DEFAULT 'manual'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto_wajah');
        });
    }
};