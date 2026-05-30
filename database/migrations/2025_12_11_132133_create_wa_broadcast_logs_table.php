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
    Schema::create('wa_broadcast_logs', function (Blueprint $table) {
        $table->id();
        $table->string('no_surat_kontrol')->nullable();
        $table->string('nama_pasien')->nullable();
        $table->string('nomor_wa')->nullable();
        $table->string('layanan')->nullable();
        $table->string('dokter')->nullable();
        $table->date('tgl_rencana')->nullable();
        $table->date('tgl_expired')->nullable();
        $table->boolean('rujukan_expired')->default(false);
        $table->text('message')->nullable();
        $table->string('status')->nullable();
        $table->text('response')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_broadcast_logs');
    }
};
