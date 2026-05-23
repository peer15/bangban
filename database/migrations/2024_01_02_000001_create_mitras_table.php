<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_usaha')->nullable();
            $table->text('alamat');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('layanan')->nullable(); // ['tambal-ban', 'isi-angin', 'ganti-ban']
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->enum('status', ['pending', 'aktif', 'nonaktif'])->default('pending');
            $table->date('subscription_sampai')->nullable();
            $table->string('foto_usaha')->nullable();
            $table->boolean('is_open')->default(false); // buka/tutup toko
            $table->boolean('is_ready')->default(true); // ready terima pesanan
            $table->string('jenis_rekening')->nullable(); // BCA, BRI, DANA, SHOPEEPAY, dll
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_layanan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};
