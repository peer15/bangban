@extends('layouts.admin')
@section('title', 'Peta Mitra')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold text-gray-800">🗺️ Peta Mitra</h2>
    <div class="flex items-center gap-3 text-sm">
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-500 rounded-full inline-block"></span> Aktif ({{ $mitras->where('status', 'aktif')->count() }})</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-500 rounded-full inline-block"></span> Pending ({{ $mitras->where('status', 'pending')->count() }})</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-500 rounded-full inline-block"></span> Nonaktif ({{ $mitras->where('status', 'nonaktif')->count() }})</span>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div id="map" class="w-full h-[500px]"></div>
</div>

<!-- Daftar Mitra di Peta -->
<div class="mt-4 bg-white rounded-xl shadow-sm p-4">
    <h3 class="font-medium text-gray-700 mb-3">Mitra dengan Lokasi ({{ $mitras->count() }})</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        @foreach($mitras as $mitra)
        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer" onclick="focusMitra({{ $mitra->latitude }}, {{ $mitra->longitude }})">
            <span class="w-3 h-3 rounded-full {{ match($mitra->status) { 'aktif' => 'bg-green-500', 'pending' => 'bg-yellow-500', 'nonaktif' => 'bg-red-500' } }}"></span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $mitra->user->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ $mitra->alamat }}</p>
            </div>
            <span class="text-xs text-gray-400">{{ number_format($mitra->latitude, 4) }}, {{ number_format($mitra->longitude, 4) }}</span>
        </div>
        @endforeach
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const mitras = @json($mitraMarkers);

let map;

document.addEventListener('DOMContentLoaded', function() {
    // Default center: Jepara
    map = L.map('map').setView([-6.5935, 110.6741], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    // Tambah marker untuk setiap mitra
    const bounds = [];

    mitras.forEach(function(mitra) {
        const color = mitra.status === 'aktif' ? '#22c55e' : (mitra.status === 'pending' ? '#eab308' : '#ef4444');

        const icon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="background:${color}; width:28px; height:28px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; font-size:12px;">🔧</div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
        });

        const marker = L.marker([mitra.lat, mitra.lng], { icon: icon }).addTo(map);

        const layananLabels = (mitra.layanan || []).map(l => {
            const map = {'tambal-ban': 'Tambal Ban', 'isi-angin': 'Isi Angin', 'ganti-ban': 'Ganti Ban'};
            return map[l] || l;
        }).join(', ');

        const fotoHtml = mitra.foto_usaha
            ? `<img src="${mitra.foto_usaha}" style="width:100%; height:100px; object-fit:cover; border-radius:6px; margin-bottom:8px;" />`
            : '';

        marker.bindPopup(`
            <div style="min-width:220px; max-width:280px;">
                ${fotoHtml}
                <p style="font-weight:700; font-size:14px; margin:0 0 2px">${mitra.name}</p>
                ${mitra.nama_usaha ? `<p style="font-size:12px; color:#555; margin:0 0 6px">${mitra.nama_usaha}</p>` : ''}
                <div style="font-size:11px; color:#666; line-height:1.6;">
                    <p style="margin:0">📍 ${mitra.alamat}</p>
                    <p style="margin:0">📧 ${mitra.email}</p>
                    <p style="margin:0">📱 ${mitra.phone || '-'}</p>
                    <p style="margin:0">🔧 ${layananLabels || '-'}</p>
                    <p style="margin:0">🕐 ${mitra.jam_buka || '-'} - ${mitra.jam_tutup || '-'}</p>
                    <p style="margin:0">⭐ ${parseFloat(mitra.rating).toFixed(1)} • ${mitra.total_layanan} layanan</p>
                    <p style="margin:0">📅 Langganan s/d: ${mitra.subscription_sampai || '-'}</p>
                </div>
                <p style="font-size:10px; margin-top:6px; padding:2px 8px; border-radius:4px; display:inline-block; background:${color}20; color:${color}; font-weight:600">${mitra.status.toUpperCase()}</p>
            </div>
        `, { maxWidth: 300 });

        bounds.push([mitra.lat, mitra.lng]);
    });

    // Fit bounds jika ada marker
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }
});

function focusMitra(lat, lng) {
    map.setView([lat, lng], 16);
}
</script>
@endsection
