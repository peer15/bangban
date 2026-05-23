<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat saldo masuk & pencairan
        Schema::create('saldo_mitras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained()->onDelete('cascade');
            $table->foreignId('pesanan_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('jenis', ['masuk', 'pencairan']);
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->enum('status', ['pending', 'selesai'])->default('selesai'); // untuk pencairan
            $table->timestamps();
        });

        // Tambah kolom saldo di tabel mitras
        Schema::table('mitras', function (Blueprint $table) {
            $table->integer('saldo')->default(0)->after('total_layanan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_mitras');
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn('saldo');
        });
    }
};
