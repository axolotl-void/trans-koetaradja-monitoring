<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('inventories', function (Blueprint $table) {

        $table->id();
        $table->string('bus_nomor_plat_ref')->nullable();
        $table->string('kode_alat')->nullable();
        $table->string('nama_alat')->nullable();
        $table->string('kategori')->nullable();
        $table->string('merk')->nullable();
        $table->string('seri')->nullable();
        $table->string('supplier')->nullable();
        $table->date('tanggal_pembelian')->nullable();
        $table->date('tanggal_masuk')->nullable();
        $table->decimal('harga_beli', 15, 2)->nullable();
        $table->string('status_barang')->nullable();
        $table->text('kondisi_awal')->nullable();
        $table->text('lokasi')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
