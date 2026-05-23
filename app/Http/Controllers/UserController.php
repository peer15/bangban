<?php

namespace App\Http\Controllers;

use App\Http\Requests\PesananRequest;
use App\Http\Requests\RatingRequest;
use App\Http\Requests\UpdateProfilRequest;
use App\Models\Pesanan;
use App\Models\Rating;
use App\Services\DokuService;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly PesananService $pesananService,
    ) {}

    public function home(): View
    {
        $userId = auth()->id();

        $pesananAktif = Pesanan::where('user_id', $userId)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->first();

        $totalPesanan = Pesanan::where('user_id', $userId)->count();
        $pesananSelesai = Pesanan::where('user_id', $userId)->where('status', 'selesai')->count();
        $totalPengeluaran = Pesanan::where('user_id', $userId)
            ->where('status', 'selesai')
            ->sum('total_biaya');

        return view('user.home', compact('pesananAktif', 'totalPesanan', 'pesananSelesai', 'totalPengeluaran'));
    }

    public function profil(): View
    {
        return view('user.profil');
    }

    public function updateProfil(UpdateProfilRequest $request): RedirectResponse
    {
        $data = $request->only(['name', 'email', 'phone']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        auth()->user()->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function pesan(Request $request): View|RedirectResponse
    {
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

        $mitras = ($lat && $lng)
            ? $this->pesananService->getMitrasTerdekat((float) $lat, (float) $lng, 5, $layanan)
            : collect();

        return view('user.pesan', compact('layanan', 'mitras'));
    }

    public function konfirmasiPesanan(PesananRequest $request): RedirectResponse
    {
        $pesananAktif = Pesanan::where('user_id', auth()->id())
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->first();

        if ($pesananAktif) {
            return redirect("/pesanan/{$pesananAktif->id}/tracking");
        }

        $pesanan = $this->pesananService->buatPesanan([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        // Jika pilih ewallet, redirect ke DOKU payment
        if ($request->pembayaran === 'ewallet') {
            $doku = app(DokuService::class);
            $user = auth()->user();

            $result = $doku->createPayment([
                'amount' => $pesanan->total_biaya,
                'customer_id' => $user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
            ]);

            if ($result['success'] && $result['payment_url']) {
                $pesanan->update([
                    'catatan_lokasi' => ($pesanan->catatan_lokasi ? $pesanan->catatan_lokasi . ' | ' : '') . 'INV:' . $result['invoice_number'],
                ]);

                return redirect($result['payment_url']);
            }
        }

        return redirect("/pesanan/{$pesanan->id}/tracking");
    }

    public function tracking(Pesanan $pesanan): View
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);
        $pesanan->load('mitra.user');

        return view('user.tracking', compact('pesanan'));
    }

    public function cancelPesanan(Pesanan $pesanan): RedirectResponse
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);
        abort_if(in_array($pesanan->status, ['selesai', 'dibatalkan']), 403);

        $pesanan->update(['status' => 'dibatalkan']);

        return redirect('/riwayat')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function riwayat(): View
    {
        $pesanans = Pesanan::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.riwayat', compact('pesanans'));
    }

    public function showRating(Pesanan $pesanan): View
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);
        abort_if($pesanan->status !== 'selesai', 404);
        $pesanan->load('mitra.user');

        return view('user.rating', compact('pesanan'));
    }

    public function simpanRating(RatingRequest $request, Pesanan $pesanan): RedirectResponse
    {
        abort_if($pesanan->user_id !== auth()->id(), 403);

        Rating::create([
            'pesanan_id' => $pesanan->id,
            'user_id' => auth()->id(),
            'mitra_id' => $pesanan->mitra_id,
            'bintang' => $request->bintang,
            'ulasan' => $request->ulasan,
        ]);

        $mitra = $pesanan->mitra;
        $mitra->update(['rating' => $mitra->ratings()->avg('bintang')]);

        return redirect('/')->with('success', 'Rating berhasil dikirim!');
    }

    public function sos(): View
    {
        return view('user.sos');
    }
}
