<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mitra_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('layanan', ['tambal-ban', 'isi-angin', 'ganti-ban']);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('catatan_lokasi')->nullable();
            $table->integer('biaya_layanan');
            $table->integer('biaya_panggil');
            $table->integer('total_biaya');
            $table->decimal('jarak_km', 5, 2)->nullable();
            $table->enum('status', [
                'mencari_mitra',
                'mitra_menuju',
                'dikerjakan',
                'selesai',
                'dibatalkan'
            ])->default('mencari_mitra');
            $table->enum('pembayaran', ['tunai', 'ewallet'])->default('tunai');
            $table->boolean('sudah_bayar')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
