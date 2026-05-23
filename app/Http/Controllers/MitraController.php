<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MitraController extends Controller
{
    public function dashboard()
    {
        $mitra = auth()->user()->mitra;
        $pesananMasuk = Pesanan::where('mitra_id', $mitra?->id)
            ->whereIn('status', ['mencari_mitra', 'mitra_menuju', 'dikerjakan'])
            ->orderBy('created_at', 'desc')
            ->get();
        $totalSelesai = Pesanan::where('mitra_id', $mitra?->id)->where('status', 'selesai')->count();
        $pendapatanBulan = Pesanan::where('mitra_id', $mitra?->id)
            ->where('status', 'selesai')
            ->where('sudah_bayar', true)
            ->whereMonth('created_at', now()->month)
            ->sum('total_biaya');

        // Auto set is_ready berdasarkan ada pesanan aktif atau tidak
        if ($mitra && $mitra->status === 'aktif') {
            $adaPesananAktif = Pesanan::where('mitra_id', $mitra->id)
                ->whereIn('status', ['mitra_menuju', 'dikerjakan'])
                ->exists();
            $mitra->update(['is_ready' => !$adaPesananAktif]);
        }

        return view('mitra.dashboard', compact('mitra', 'pesananMasuk', 'totalSelesai', 'pendapatanBulan'));
    }

    public function toggleOpen()
    {
        $mitra = auth()->user()->mitra;
        $mitra->update(['is_open' => !$mitra->is_open]);

        return back()->with('success', $mitra->is_open ? 'Toko dibuka! Kamu bisa menerima pesanan.' : 'Toko ditutup.');
    }

    public function pesananMasuk()
    {
        $mitra = auth()->user()->mitra;
        $pesanans = Pesanan::where('status', 'mencari_mitra')
            ->whereNull('mitra_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mitra.pesanan', compact('pesanans', 'mitra'));
    }

    /**
     * API: Check for incoming orders (polling for popup)
     */
    public function checkIncoming()
    {
        $mitra = auth()->user()->mitra;
        if (!$mitra || $mitra->status !== 'aktif' || !$mitra->is_open) {
            return response()->json(['has_order' => false]);
        }

        $pesanan = Pesanan::where('status', 'mencari_mitra')
            ->whereNull('mitra_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$pesanan) {
            return response()->json(['has_order' => false]);
        }

        // Calculate distance if mitra has coordinates
        $jarak = null;
        if ($mitra->latitude && $mitra->longitude && $pesanan->latitude && $pesanan->longitude) {
            $jarak = round(6371 * acos(
                cos(deg2rad($mitra->latitude)) *
                cos(deg2rad($pesanan->latitude)) *
                cos(deg2rad($pesanan->longitude) - deg2rad($mitra->longitude)) +
                sin(deg2rad($mitra->latitude)) *
                sin(deg2rad($pesanan->latitude))
            ), 1);
        }

        return response()->json([
            'has_order' => true,
            'pesanan' => [
                'id' => $pesanan->id,
                'nama_layanan' => $pesanan->nama_layanan,
                'total_biaya' => $pesanan->total_biaya,
                'pembayaran' => $pesanan->pembayaran,
                'catatan_lokasi' => $pesanan->catatan_lokasi,
                'jarak_km' => $jarak,
                'user_name' => $pesanan->user->name,
                'created_at' => $pesanan->created_at->diffForHumans(),
            ],
        ]);
    }

    public function terimaPesanan(Pesanan $pesanan)
    {
        $mitra = auth()->user()->mitra;
        abort_if(!$mitra || $mitra->status !== 'aktif', 403);

        $pesanan->update([
            'mitra_id' => $mitra->id,
            'status' => 'mitra_menuju',
        ]);

        return redirect('/mitra/pesanan')->with('success', 'Pesanan diterima!');
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $mitra = auth()->user()->mitra;
        abort_if($pesanan->mitra_id !== $mitra->id, 403);

        $request->validate(['status' => 'required|in:mitra_menuju,dikerjakan,selesai']);

        $pesanan->update(['status' => $request->status]);

        if ($request->status === 'selesai') {
            $pesanan->update(['sudah_bayar' => true]);
            $mitra->increment('total_layanan');

            // Jika pembayaran online, tambah saldo mitra
            if ($pesanan->pembayaran === 'ewallet') {
                \App\Models\SaldoMitra::create([
                    'mitra_id' => $mitra->id,
                    'pesanan_id' => $pesanan->id,
                    'jenis' => 'masuk',
                    'jumlah' => $pesanan->total_biaya,
                    'keterangan' => 'Pesanan #' . $pesanan->id . ' - ' . $pesanan->nama_layanan,
                ]);
                $mitra->increment('saldo', $pesanan->total_biaya);
            }
        }

        return back()->with('success', 'Status diperbarui!');
    }

    public function cancelPesanan(Pesanan $pesanan)
    {
        $mitra = auth()->user()->mitra;
        abort_if($pesanan->mitra_id !== $mitra->id, 403);
        abort_if(in_array($pesanan->status, ['selesai', 'dibatalkan']), 403);

        $pesanan->update([
            'status' => 'dibatalkan',
            'mitra_id' => null,
        ]);

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function saldo()
    {
        $mitra = auth()->user()->mitra;
        $riwayat = \App\Models\SaldoMitra::where('mitra_id', $mitra->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('mitra.saldo', compact('mitra', 'riwayat'));
    }

    public function cairkanSaldo()
    {
        $mitra = auth()->user()->mitra;
        abort_if($mitra->saldo < 50000, 403, 'Saldo minimum pencairan Rp 50.000');

        $jumlah = $mitra->saldo;
        $potongan = (int) ceil($jumlah * 0.01); // 1% potongan
        $diterima = $jumlah - $potongan;

        \App\Models\SaldoMitra::create([
            'mitra_id' => $mitra->id,
            'jenis' => 'pencairan',
            'jumlah' => $diterima,
            'keterangan' => 'Pencairan Rp ' . number_format($jumlah, 0, ',', '.') . ' - Potongan 1% (Rp ' . number_format($potongan, 0, ',', '.') . ')',
            'status' => 'pending',
        ]);

        $mitra->update(['saldo' => 0]);

        return back()->with('success', 'Pencairan diproses! Total Rp ' . number_format($jumlah, 0, ',', '.') . ' - Potongan 1% (Rp ' . number_format($potongan, 0, ',', '.') . ') = Diterima Rp ' . number_format($diterima, 0, ',', '.'));
    }

    public function riwayat()
    {
        $mitra = auth()->user()->mitra;
        $pesanans = Pesanan::where('mitra_id', $mitra->id)
            ->where('status', 'selesai')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mitra.riwayat', compact('pesanans'));
    }

    public function profil()
    {
        $mitra = auth()->user()->mitra;
        return view('mitra.profil', compact('mitra'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nama_usaha' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'layanan' => 'required|array',
            'jam_buka' => 'nullable',
            'jam_tutup' => 'nullable',
            'foto_usaha' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jenis_rekening' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'nama_rekening' => 'nullable|string|max:255',
        ]);

        $mitra = auth()->user()->mitra;
        $data = $request->only([
            'nama_usaha', 'alamat', 'latitude', 'longitude', 'layanan', 'jam_buka', 'jam_tutup',
            'jenis_rekening', 'nomor_rekening', 'nama_rekening',
        ]);

        if ($request->hasFile('foto_usaha')) {
            if ($mitra->foto_usaha && \Storage::disk('public')->exists($mitra->foto_usaha)) {
                \Storage::disk('public')->delete($mitra->foto_usaha);
            }
            $data['foto_usaha'] = $request->file('foto_usaha')->store('foto-usaha', 'public');
        }

        $mitra->update($data);

        return back()->with('success', 'Profil diperbarui!');
    }

    public function updateFotoProfil(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();

        // Hapus foto lama
        if ($user->foto_profil && \Storage::disk('public')->exists($user->foto_profil)) {
            \Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto_profil')->store('foto-profil', 'public');
        $user->update(['foto_profil' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    // Form pendaftaran mitra (publik)
    public function showDaftar()
    {
        return view('mitra.daftar');
    }

    public function daftar(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
            'alamat' => 'required|string',
            'layanan' => 'required|array',
            'foto_usaha' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'jam_buka' => 'nullable',
            'jam_tutup' => 'nullable',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $fotoPath = $request->file('foto_usaha')->store('foto-usaha', 'public');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'mitra',
            'password' => Hash::make($request->password),
        ]);

        Mitra::create([
            'user_id' => $user->id,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'layanan' => $request->layanan,
            'jam_buka' => $request->jam_buka,
            'jam_tutup' => $request->jam_tutup,
            'foto_usaha' => $fotoPath,
            'status' => 'pending',
        ]);

        Auth::login($user);

        return redirect('/mitra/pembayaran')->with('success', 'Pendaftaran berhasil! Silakan selesaikan pembayaran.');
    }
}
