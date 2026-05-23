<?php

namespace App\Services;

use App\Models\Mitra;
use App\Models\SaldoMitra;

class SaldoService
{
    private const MINIMUM_PENCAIRAN = 50000;
    private const POTONGAN_PERSEN = 0.01;

    /**
     * Proses pencairan saldo mitra.
     *
     * @return array{jumlah: int, potongan: int, diterima: int}
     */
    public function cairkan(Mitra $mitra): array
    {
        abort_if($mitra->saldo < self::MINIMUM_PENCAIRAN, 403, 'Saldo minimum pencairan Rp 50.000');

        $jumlah = $mitra->saldo;
        $potongan = (int) ceil($jumlah * self::POTONGAN_PERSEN);
        $diterima = $jumlah - $potongan;

        SaldoMitra::create([
            'mitra_id' => $mitra->id,
            'jenis' => 'pencairan',
            'jumlah' => $diterima,
            'keterangan' => "Pencairan Rp " . number_format($jumlah, 0, ',', '.') .
                " - Potongan 1% (Rp " . number_format($potongan, 0, ',', '.') . ")",
            'status' => 'pending',
        ]);

        $mitra->update(['saldo' => 0]);

        return compact('jumlah', 'potongan', 'diterima');
    }
}
