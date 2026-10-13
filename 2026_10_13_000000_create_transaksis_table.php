<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nomor_kuitansi')
                ->unique()
                ->nullable();

            $table->string('nama_pelanggan');
            $table->date('tanggal');
            $table->text('keterangan');

            $table->unsignedBigInteger('nominal');

            $table->string('metode_pembayaran');
            $table->string('status')
                ->default('belum_lunas');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
