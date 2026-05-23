<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\PembayaranMitra;
use App\Models\Pesanan;
use App\Models\SaldoMitra;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $totalUser = User::where('role', 'user')->count();
        $totalMitra = Mitra::count();
        $mitraAktif = Mitra::where('status', 'aktif')->count();
        $mitraPending = Mitra::where('status', 'pending')->count();
        $totalPesanan = Pesanan::count();
        $pesananHariIni = Pesanan::whereDate('created_at', today())->count();

        $pendapatanBulan = PembayaranMitra::where('status', 'lunas')
            ->whereMonth('created_at', now()->month)
            ->sum('jumlah');

        $totalSaldoDoku = Pesanan::where('pembayaran', 'ewallet')
            ->where('status', 'selesai')
            ->where('sudah_bayar', true)
            ->sum('total_biaya');

        $totalDicairkan = SaldoMitra::where('jenis', 'pencairan')
            ->where('status', 'selesai')
            ->sum('jumlah');

        $pencairanPending = SaldoMitra::with('mitra.user')
            ->where('jenis', 'pencairan')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $saldoBangban = $totalSaldoDoku - $totalDicairkan;

        return view('admin.dashboard', compact(
            'totalUser', 'totalMitra', 'mitraAktif', 'mitraPending',
            'totalPesanan', 'pesananHariIni', 'pendapatanBulan',
            'totalSaldoDoku', 'totalDicairkan', 'saldoBangban',
            'pencairanPending'
        ));
    }

    public function mitraList(): View
    {
        $mitras = Mitra::with('user')
            ->whereIn('status', ['aktif', 'nonaktif'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.mitra.index', compact('mitras'));
    }

    public function mitraPending(): View
    {
        $mitras = Mitra::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.mitra.pending', compact('mitras'));
    }

    public function mitraVerifikasi(Mitra $mitra): RedirectResponse
    {
        $mitra->update([
            'status' => 'aktif',
            'subscription_sampai' => now()->addMonth(),
        ]);

        PembayaranMitra::create([
            'mitra_id' => $mitra->id,
            'jenis' => 'pendaftaran',
            'jumlah' => 250000,
            'status' => 'lunas',
            'periode_mulai' => now(),
            'periode_selesai' => now()->addMonth(),
        ]);

        return back()->with('success', "Mitra {$mitra->user->name} berhasil diverifikasi!");
    }

    public function mitraNonaktif(Mitra $mitra): RedirectResponse
    {
        $mitra->update(['status' => 'nonaktif']);
        return back()->with('success', 'Mitra dinonaktifkan.');
    }

    public function pesananList(): View
    {
        $pesanans = Pesanan::with(['user', 'mitra.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pesanan.index', compact('pesanans'));
    }

    public function userList(): View
    {
        $users = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.user.index', compact('users'));
    }

    public function pembayaranList(): View
    {
        $pembayarans = PembayaranMitra::with('mitra.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function petaMitra(): View
    {
        $mitras = Mitra::with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $mitraMarkers = $mitras->map(fn (Mitra $m) => [
            'id' => $m->id,
            'name' => $m->user->name,
            'nama_usaha' => $m->nama_usaha,
            'alamat' => $m->alamat,
            'lat' => $m->latitude,
            'lng' => $m->longitude,
            'status' => $m->status,
            'rating' => $m->rating,
            'total_layanan' => $m->total_layanan,
            'phone' => $m->user->phone,
            'email' => $m->user->email,
            'foto_usaha' => $m->foto_usaha ? asset('storage/' . $m->foto_usaha) : null,
            'layanan' => $m->layanan ?? [],
            'jam_buka' => $m->jam_buka,
            'jam_tutup' => $m->jam_tutup,
            'subscription_sampai' => $m->subscription_sampai?->format('d M Y'),
        ]);

        return view('admin.peta', compact('mitras', 'mitraMarkers'));
    }

    public function pencairanList(): View
    {
        $pencairanPending = SaldoMitra::with('mitra.user')
            ->where('jenis', 'pencairan')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $semuaPencairan = SaldoMitra::with('mitra.user')
            ->where('jenis', 'pencairan')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pencairan.index', compact('pencairanPending', 'semuaPencairan'));
    }

    public function pencairanSelesai(SaldoMitra $pencairan): RedirectResponse
    {
        $pencairan->update(['status' => 'selesai']);
        return back()->with('success', 'Pencairan ditandai sudah ditransfer.');
    }
}
