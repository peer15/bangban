<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_mitras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained()->onDelete('cascade');
            $table->enum('jenis', ['pendaftaran', 'langganan']);
            $table->integer('jumlah'); // 250000 atau 150000
            $table->string('invoice_number')->nullable();
            $table->string('metode_pembayaran')->nullable(); // QRIS, SHOPEEPAY, BCA_VA, dll
            $table->enum('status', ['pending', 'lunas', 'gagal'])->default('pending');
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_mitras');
    }
};
