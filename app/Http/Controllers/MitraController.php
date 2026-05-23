<?php

namespace App\Http\Controllers;

use App\Http\Requests\DaftarMitraRequest;
use App\Http\Requests\UpdateMitraProfilRequest;
use App\Models\Mitra;
use App\Models\Pesanan;
use App\Models\SaldoMitra;
use App\Models\User;
use App\Services\PesananService;
use App\Services\SaldoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MitraController extends Controller
{
    public function __construct(
        private readonly PesananService $pesananService,
        private readonly SaldoService $saldoService,
    ) {}

    public function dashboard(): View
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
        if ($mitra?->status === 'aktif') {
            $adaPesananAktif = Pesanan::where('mitra_id', $mitra->id)
                ->whereIn('status', ['mitra_menuju', 'dikerjakan'])
                ->exists();
            $mitra->update(['is_ready' => !$adaPesananAktif]);
        }

        return view('mitra.dashboard', compact('mitra', 'pesananMasuk', 'totalSelesai', 'pendapatanBulan'));
    }

    public function toggleOpen(): RedirectResponse
    {
        $mitra = auth()->user()->mitra;
        $mitra->update(['is_open' => !$mitra->is_open]);

        $message = $mitra->is_open
            ? 'Toko dibuka! Kamu bisa menerima pesanan.'
            : 'Toko ditutup.';

        return back()->with('success', $message);
    }

    public function pesananMasuk(): View
    {
        $mitra = auth()->user()->mitra;

        $pesanans = Pesanan::where('status', 'mencari_mitra')
            ->whereNull('mitra_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mitra.pesanan', compact('pesanans', 'mitra'));
    }

    public function checkIncoming(): JsonResponse
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

        $jarak = null;
        if ($mitra->latitude && $mitra->longitude && $pesanan->latitude && $pesanan->longitude) {
            $jarak = $this->pesananService->hitungJarak(
                $mitra->latitude,
                $mitra->longitude,
                $pesanan->latitude,
                $pesanan->longitude
            );
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

    public function terimaPesanan(Pesanan $pesanan): RedirectResponse
    {
        $mitra = auth()->user()->mitra;
        abort_if(!$mitra || $mitra->status !== 'aktif', 403);

        $pesanan->update([
            'mitra_id' => $mitra->id,
            'status' => 'mitra_menuju',
        ]);

        return redirect('/mitra/pesanan')->with('success', 'Pesanan diterima!');
    }

    public function updateStatus(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $mitra = auth()->user()->mitra;
        abort_if($pesanan->mitra_id !== $mitra->id, 403);

        $request->validate(['status' => 'required|in:mitra_menuju,dikerjakan,selesai']);

        if ($request->status === 'selesai') {
            $this->pesananService->selesaikanPesanan($pesanan, $mitra);
        } else {
            $pesanan->update(['status' => $request->status]);
        }

        return back()->with('success', 'Status diperbarui!');
    }

    public function cancelPesanan(Pesanan $pesanan): RedirectResponse
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

    public function saldo(): View
    {
        $mitra = auth()->user()->mitra;
        $riwayat = SaldoMitra::where('mitra_id', $mitra->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('mitra.saldo', compact('mitra', 'riwayat'));
    }

    public function cairkanSaldo(): RedirectResponse
    {
        $mitra = auth()->user()->mitra;
        $result = $this->saldoService->cairkan($mitra);

        $message = "Pencairan diproses! Total Rp " . number_format($result['jumlah'], 0, ',', '.') .
            " - Potongan 1% (Rp " . number_format($result['potongan'], 0, ',', '.') .
            ") = Diterima Rp " . number_format($result['diterima'], 0, ',', '.');

        return back()->with('success', $message);
    }

    public function riwayat(): View
    {
        $mitra = auth()->user()->mitra;
        $pesanans = Pesanan::where('mitra_id', $mitra->id)
            ->where('status', 'selesai')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mitra.riwayat', compact('pesanans'));
    }

    public function profil(): View
    {
        $mitra = auth()->user()->mitra;
        return view('mitra.profil', compact('mitra'));
    }

    public function updateProfil(UpdateMitraProfilRequest $request): RedirectResponse
    {
        $mitra = auth()->user()->mitra;
        $data = $request->only([
            'nama_usaha', 'alamat', 'latitude', 'longitude', 'layanan',
            'jam_buka', 'jam_tutup', 'jenis_rekening', 'nomor_rekening', 'nama_rekening',
        ]);

        if ($request->hasFile('foto_usaha')) {
            if ($mitra->foto_usaha && Storage::disk('public')->exists($mitra->foto_usaha)) {
                Storage::disk('public')->delete($mitra->foto_usaha);
            }
            $data['foto_usaha'] = $request->file('foto_usaha')->store('foto-usaha', 'public');
        }

        $mitra->update($data);

        return back()->with('success', 'Profil diperbarui!');
    }

    public function updateFotoProfil(Request $request): RedirectResponse
    {
        $request->validate([
            'foto_profil' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user = auth()->user();

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto_profil')->store('foto-profil', 'public');
        $user->update(['foto_profil' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function showDaftar(): View
    {
        return view('mitra.daftar');
    }

    public function daftar(DaftarMitraRequest $request): RedirectResponse
    {
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
