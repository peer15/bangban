<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Pesanan;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function home()
    {
        // Cek apakah ada pesanan aktif
        $pesananAktif = Pesanan::where('user_id', auth()->id())
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->first();

        // Stats user
        $totalPesanan = Pesanan::where('user_id', auth()->id())->count();
        $pesananSelesai = Pesanan::where('user_id', auth()->id())->where('status', 'selesai')->count();
        $totalPengeluaran = Pesanan::where('user_id', auth()->id())
            ->where('status', 'selesai')
            ->sum('total_biaya');

        return view('user.home', compact('pesananAktif', 'totalPesanan', 'pesananSelesai', 'totalPengeluaran'));
    }

    public function profil()
    {
        return view('user.profil');
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'phone']);

        if ($request->filled('password')) {
            $data['password'] = \Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function pesan(Request $request)
    {
        // Cek apakah sudah ada pesanan aktif
        $pesananAktif = Pesanan::where('user_id', auth()->id())
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->first();

        if ($pesananAktif) {
            return redirect("/pesanan/{$pesananAktif->id}/tracking")
                ->with('success', 'Kamu masih punya pesanan aktif.');
        }

        $layanan = $request->get('layanan', 'tambal-ban');
        $lat = $request->get('lat');
        $lng = $request->get('lng');

        // Cari mitra terdekat dalam radius 5km menggunakan Haversine
        if ($lat && $lng) {
            $mitras = $this->getMitrasTerdekat($lat, $lng, 5, $layanan);
        } else {
            $mitras = collect();
        }

        return view('user.pesan', compact('layanan', 'mitras'));
    }

    public function konfirmasiPesanan(Request $request)
    {
        // Cek pesanan aktif
        $pesananAktif = Pesanan::where('user_id', auth()->id())
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->first();

        if ($pesananAktif) {
            return redirect("/pesanan/{$pesananAktif->id}/tracking");
        }

        $request->validate([
            'layanan' => 'required|in:tambal-ban,isi-angin,ganti-ban',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'catatan_lokasi' => 'nullable|string|max:500',
            'pembayaran' => 'required|in:tunai,ewallet',
        ]);

        $biayaLayanan = match($request->layanan) {
            'tambal-ban' => 35000,
            'isi-angin' => 5000,
            'ganti-ban' => 80000,
            default => 35000,
        };

        // Cari mitra terdekat (Haversine, max 5km) - untuk estimasi biaya panggil
        $mitrasTerdekat = $this->getMitrasTerdekat(
            $request->latitude,
            $request->longitude,
            5,
            $request->layanan
        );

        $mitraTerdekat = $mitrasTerdekat->first();
        $jarakKm = $mitraTerdekat ? $mitraTerdekat->jarak_km : null;

        // Biaya panggil berdasarkan jarak (Rp 5.000 per km, min Rp 5.000)
        $biayaPanggil = $jarakKm ? max(5000, ceil($jarakKm) * 5000) : 10000;
        $totalBiaya = $biayaLayanan + $biayaPanggil;

        // Pesanan dibuat dengan status mencari_mitra, mitra harus acc manual
        $pesanan = Pesanan::create([
            'user_id' => auth()->id(),
            'mitra_id' => null,
            'layanan' => $request->layanan,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'catatan_lokasi' => $request->catatan_lokasi,
            'biaya_layanan' => $biayaLayanan,
            'biaya_panggil' => $biayaPanggil,
            'total_biaya' => $totalBiaya,
            'jarak_km' => $jarakKm,
            'pembayaran' => $request->pembayaran,
            'status' => 'mencari_mitra',
        ]);

        // Jika pilih ewallet, redirect ke DOKU payment
        if ($request->pembayaran === 'ewallet') {
            $doku = app(\App\Services\DokuService::class);
            $user = auth()->user();

            $result = $doku->createPayment([
                'amount' => $totalBiaya,
                'customer_id' => $user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
            ]);

            if ($result['success'] && $result['payment_url']) {
                // Simpan invoice di pesanan (bisa pakai catatan atau field baru)
                $pesanan->update(['catatan_lokasi' => ($pesanan->catatan_lokasi ? $pesanan->catatan_lokasi . ' | ' : '') . 'INV:' . $result['invoice_number']]);
                return redirect($result['payment_url']);
            }
        }

        return redirect("/pesanan/{$pesanan->id}/tracking");
    }

    /**
     * Cari mitra terdekat menggunakan Haversine Formula
     *
     * @param float $lat Latitude user
     * @param float $lng Longitude user
     * @param float $radiusKm Radius pencarian dalam km
     * @param string|null $layanan Filter layanan tertentu
     */
    private function getMitrasTerdekat(float $lat, float $lng, float $radiusKm = 5, ?string $layanan = null)
    {
        $haversine = "(6371 * acos(
            cos(radians(?)) *
            cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) *
            sin(radians(latitude))
        ))";

        $query = Mitra::select('mitras.*')
            ->selectRaw("{$haversine} AS jarak_km", [$lat, $lng, $lat])
            ->where('status', 'aktif')
            ->where('is_open', true)
            ->where('is_ready', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('jarak_km', '<=', $radiusKm)
            ->orderBy('jarak_km', 'asc');

        // Filter berdasarkan layanan yang diminta
        if ($layanan) {
            $query->whereRaw("JSON_CONTAINS(layanan, ?)", [json_encode($layanan)]);
        }

        return $query->with('user')->limit(5)->get();
    }

    public function tracking(Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);
        $pesanan->load('mitra.user');

        return view('user.tracking', compact('pesanan'));
    }

    public function cancelPesanan(Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);
        abort_if(in_array($pesanan->status, ['selesai', 'dibatalkan']), 403);

        $pesanan->update(['status' => 'dibatalkan']);

        return redirect('/riwayat')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function riwayat()
    {
        $pesanans = Pesanan::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.riwayat', compact('pesanans'));
    }

    public function showRating(Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);
        abort_if($pesanan->status !== 'selesai', 404);
        $pesanan->load('mitra.user');

        return view('user.rating', compact('pesanan'));
    }

    public function simpanRating(Request $request, Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        $request->validate([
            'bintang' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000',
        ]);

        Rating::create([
            'pesanan_id' => $pesanan->id,
            'user_id' => auth()->id(),
            'mitra_id' => $pesanan->mitra_id,
            'bintang' => $request->bintang,
            'ulasan' => $request->ulasan,
        ]);

        $mitra = $pesanan->mitra;
        $mitra->rating = $mitra->ratings()->avg('bintang');
        $mitra->save();

        return redirect('/')->with('success', 'Rating berhasil dikirim!');
    }

    public function sos()
    {
        return view('user.sos');
    }
}
