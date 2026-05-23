@extends('layouts.mitra')
@section('title', 'Profil')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="max-w-5xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola profil dan pengaturan mitra</p>
    </div>

    <!-- Foto Profil -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-4">Foto Profil</h3>
        <div class="flex items-center gap-5">
            @if(auth()->user()->foto_profil)
                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-gray-200">
            @else
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white text-2xl font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            @endif
            <form method="POST" action="/mitra/profil/foto" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <label class="cursor-pointer px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                    Pilih Foto
                    <input type="file" name="foto_profil" accept="image/jpeg,image/png" class="hidden" onchange="this.form.submit()">
                </label>
                <p class="text-xs text-gray-400">JPG/PNG, maks 2MB</p>
            </form>
        </div>
    </div>

    <!-- Form Profil -->
    <form method="POST" action="/mitra/profil" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-900 mb-2">Informasi Usaha</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha</label>
                <input type="text" name="nama_usaha" value="{{ $mitra->nama_usaha ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm h-20 resize-none focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400">{{ $mitra->alamat ?? '' }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Usaha</label>
                @if($mitra->foto_usaha)
                    <img src="{{ asset('storage/' . $mitra->foto_usaha) }}" class="w-full h-40 object-cover rounded-xl border border-gray-200 mb-2">
                @endif
                <div id="foto-preview-container" class="hidden mb-2">
                    <img id="foto-preview" class="w-full h-40 object-cover rounded-xl border border-gray-200">
                </div>
                <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-red-400 hover:bg-red-50 transition">
                    <p class="text-xs text-gray-500">{{ $mitra->foto_usaha ? 'Ganti foto' : 'Upload foto bengkel' }}</p>
                    <p class="text-xs text-gray-400">JPG, PNG (maks. 2MB)</p>
                    <input type="file" name="foto_usaha" accept="image/jpeg,image/png" class="hidden" onchange="previewFoto(this)">
                </label>
            </div>
        </div>

        <!-- Lokasi -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Lokasi Usaha</h3>
            <div class="flex rounded-xl border border-gray-200 overflow-hidden mb-3">
                <button type="button" id="tab-map" onclick="switchTab('map')" class="flex-1 py-2.5 text-xs font-medium bg-red-600 text-white transition">📍 Pilih dari Peta</button>
                <button type="button" id="tab-manual" onclick="switchTab('manual')" class="flex-1 py-2.5 text-xs font-medium bg-white text-gray-600 transition">✏️ Input Koordinat</button>
            </div>
            <div id="section-map">
                <div id="map" class="w-full h-52 rounded-xl border border-gray-200 z-0"></div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray-400">Klik peta atau geser marker</p>
                    <button type="button" onclick="goToMyLocation()" class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition">📍 Lokasi Saya</button>
                </div>
            </div>
            <div id="section-manual" class="hidden">
                <div class="flex gap-2">
                    <input type="text" id="manual-lat" value="{{ $mitra->latitude ?? '' }}" placeholder="Latitude" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" oninput="updateFromManual()">
                    <input type="text" id="manual-lng" value="{{ $mitra->longitude ?? '' }}" placeholder="Longitude" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" oninput="updateFromManual()">
                </div>
            </div>
            <input type="hidden" name="latitude" id="input-lat" value="{{ $mitra->latitude ?? '' }}">
            <input type="hidden" name="longitude" id="input-lng" value="{{ $mitra->longitude ?? '' }}">
            <div id="coords-display" class="mt-2 text-xs text-red-600 font-medium {{ ($mitra->latitude ?? null) ? '' : 'hidden' }}">
                ✓ Lokasi: <span id="coords-text">{{ ($mitra->latitude ?? '') . ', ' . ($mitra->longitude ?? '') }}</span>
            </div>
        </div>

        <!-- Layanan & Jam -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-900 mb-2">Layanan & Jam Operasional</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Layanan</label>
                <div class="space-y-2">
                    @foreach(['tambal-ban' => 'Tambal Ban', 'isi-angin' => 'Isi Angin/Nitrogen', 'ganti-ban' => 'Ganti Ban'] as $val => $label)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="layanan[]" value="{{ $val }}" {{ in_array($val, $mitra->layanan ?? []) ? 'checked' : '' }} class="rounded border-gray-300 text-red-500 focus:ring-red-200">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Buka</label>
                    <input type="time" name="jam_buka" value="{{ $mitra->jam_buka ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Tutup</label>
                    <input type="time" name="jam_tutup" value="{{ $mitra->jam_tutup ?? '' }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
                </div>
            </div>
        </div>

        <!-- Rekening -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-900 mb-2">Rekening Pencairan</h3>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Jenis Rekening / E-Wallet</label>
                <select name="jenis_rekening" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
                    <option value="">-- Pilih --</option>
                    @foreach(['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI', 'DANA', 'ShopeePay', 'OVO', 'GoPay', 'LinkAja'] as $bank)
                        <option value="{{ $bank }}" {{ ($mitra->jenis_rekening ?? '') === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Nomor Rekening</label>
                <input type="text" name="nomor_rekening" value="{{ $mitra->nomor_rekening ?? '' }}" placeholder="1234567890" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Nama Pemilik</label>
                <input type="text" name="nama_rekening" value="{{ $mitra->nama_rekening ?? '' }}" placeholder="Nama sesuai rekening" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
            </div>
        </div>

        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3.5 rounded-xl transition">Simpan Profil</button>
    </form>

    <!-- Status Langganan -->
    @if($mitra)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
        <h3 class="font-semibold text-gray-900 mb-2">Status Langganan</h3>
        <p class="text-sm text-gray-600">Status: <span class="font-medium {{ $mitra->status === 'aktif' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst($mitra->status) }}</span></p>
        @if($mitra->subscription_sampai)
            <p class="text-sm text-gray-600">Berlaku sampai: {{ $mitra->subscription_sampai->format('d M Y') }}</p>
        @endif
    </div>
    @endif
</div>

@push('scripts')
<script>
let map, marker;
const initLat = {{ $mitra->latitude ?? -6.5935 }};
const initLng = {{ $mitra->longitude ?? 110.6741 }};

function initMap() {
    map = L.map('map').setView([initLat, initLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'', maxZoom:19}).addTo(map);
    marker = L.marker([initLat, initLng], {draggable:true}).addTo(map);
    map.on('click', function(e) { setLocation(e.latlng.lat, e.latlng.lng); });
    marker.on('dragend', function() { const p = marker.getLatLng(); setLocation(p.lat, p.lng); });
}
function setLocation(lat, lng) {
    marker.setLatLng([lat, lng]);
    document.getElementById('input-lat').value = lat.toFixed(7);
    document.getElementById('input-lng').value = lng.toFixed(7);
    document.getElementById('manual-lat').value = lat.toFixed(7);
    document.getElementById('manual-lng').value = lng.toFixed(7);
    document.getElementById('coords-display').classList.remove('hidden');
    document.getElementById('coords-text').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
        .then(r=>r.json()).then(d=>{if(d.display_name){const a=document.querySelector('textarea[name="alamat"]');if(a)a.value=d.display_name;}}).catch(()=>{});
}
function updateFromManual() {
    const lat=parseFloat(document.getElementById('manual-lat').value), lng=parseFloat(document.getElementById('manual-lng').value);
    if(!isNaN(lat)&&!isNaN(lng)){setLocation(lat,lng);map.setView([lat,lng],15);}
}
function goToMyLocation() {
    if(navigator.geolocation){navigator.geolocation.getCurrentPosition(p=>{map.setView([p.coords.latitude,p.coords.longitude],17);setLocation(p.coords.latitude,p.coords.longitude);},()=>alert('Gagal mendeteksi lokasi.'));}
}
function switchTab(tab) {
    if(tab==='map'){document.getElementById('tab-map').className='flex-1 py-2.5 text-xs font-medium bg-red-600 text-white transition';document.getElementById('tab-manual').className='flex-1 py-2.5 text-xs font-medium bg-white text-gray-600 transition';document.getElementById('section-map').classList.remove('hidden');document.getElementById('section-manual').classList.add('hidden');setTimeout(()=>map.invalidateSize(),100);}
    else{document.getElementById('tab-manual').className='flex-1 py-2.5 text-xs font-medium bg-red-600 text-white transition';document.getElementById('tab-map').className='flex-1 py-2.5 text-xs font-medium bg-white text-gray-600 transition';document.getElementById('section-manual').classList.remove('hidden');document.getElementById('section-map').classList.add('hidden');}
}
function previewFoto(input) {
    if(input.files&&input.files[0]){const r=new FileReader();r.onload=e=>{document.getElementById('foto-preview').src=e.target.result;document.getElementById('foto-preview-container').classList.remove('hidden');};r.readAsDataURL(input.files[0]);}
}
document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush
@endsection
