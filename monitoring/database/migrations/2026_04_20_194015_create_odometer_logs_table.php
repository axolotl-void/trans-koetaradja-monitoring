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
       Schema::create('odometer_logs', function (Blueprint $table) {
    $table->id();
    $table->string('bus_nomor_plat_ref');
    $table->date('tanggal_update');
    $table->integer('kilometer');
    $table->string('user_nama')->nullable();
    $table->text('catatan')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odometer_logs');
    }
};
