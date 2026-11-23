<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
    $table->id();

    // BUS
    $table->string('bus_plat');

    // KOMOPNEN LAMA
    $table->string('komponen_lama_kode');
    $table->string('komponen_lama_nama')->nullable();

    // KOMOPNEN BARU
    $table->string('komponen_baru_kode');
    $table->string('komponen_baru_nama')->nullable();

    // TAMBAHAN PENTING (FIX LEVEL SYSTEM)
    $table->integer('km_saat_ganti')->nullable(); // 🔥 penting
    $table->string('user_petugas')->nullable();    // 🔥 siapa yg ganti

    // CATATAN
    $table->text('catatan')->nullable();

    // WAKTU
    $table->timestamp('tanggal_ganti')->useCurrent();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};