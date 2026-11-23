<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitoring_assets', function (Blueprint $table) {
    $table->id();
    $table->string('inventory_kode_alat_ref');
    $table->string('bus_nomor_plat_ref');

    $table->date('tanggal_acuan')->nullable();
    $table->integer('km_acuan')->nullable();

    $table->integer('batas_waktu_bulan')->nullable();
    $table->integer('batas_km')->nullable();

    $table->enum('jenis_monitoring', ['waktu', 'km', 'hybrid'])->default('hybrid');

    $table->integer('warning_hari')->nullable();
    $table->integer('warning_km')->nullable();

    $table->enum('status_monitoring', ['aman', 'peringatan', 'wajib_ganti'])->default('aman');

    $table->date('tanggal_expired')->nullable();
    $table->integer('km_expired')->nullable();

    $table->text('catatan')->nullable();

    $table->timestamp('last_checked_at')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_assets');
    }
};
