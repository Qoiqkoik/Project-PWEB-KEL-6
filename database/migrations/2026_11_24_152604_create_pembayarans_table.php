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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penghuni_id')->constrained('penghunis')->onDelete('cascade');
            $table->tinyInteger('periode_bulan');
            $table->smallInteger('periode_tahun');
            $table->integer('jumlah');
            $table->date('jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('metode', ['tunai', 'transfer'])->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->enum('status', ['belum_bayar', 'lunas', 'terlambat'])->default('belum_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
