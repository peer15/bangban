<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Mitra - BANGBAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans py-8 px-4">
    <div class="max-w-sm mx-auto">
        <div class="text-center mb-6">
            <img src="/logo.jpg" alt="BANGBAN" class="h-14 w-14 object-cover mx-auto mb-2">
            <h1 class="text-2xl font-bold text-orange-500">BANGBAN</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar sebagai Mitra Teknisi</p>
        </div>

        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
            <h3 class="font-semibold text-orange-800 text-sm mb-2">Keuntungan Mitra</h3>
            <ul class="text-sm text-orange-700 space-y-1">
                <li>✓ Dapat pelanggan baru otomatis</li>
                <li>✓ Biaya pendaftaran Rp 250.000</li>
                <li>✓ Langganan bulanan Rp 150.000</li>
                <li>✓ Termasuk starter kit alat tambal</li>
            </ul>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/daftar-mitra" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WhatsApp</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>

            <!-- Alamat -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Usaha</label>
                <textarea name="alamat" id="alamat" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm h-20 resize-none focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('alamat') }}</textarea>
            </div>

            <!-- Foto Usaha -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Usaha / Bengkel <span class="text-red-500">*</span></label>
                <div id="foto-preview-container" class="hidden mb-2">
                    <img id="foto-preview" class="w-full h-40 object-cover rounded-lg border border-gray-200" alt="Preview foto">
                </div>
                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition">
                    <div class="flex flex-col items-center justify-center pt-2 pb-2">
                        <svg class="w-8 h-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-xs text-gray-500">Klik untuk upload foto bengkel</p>
                        <p class="text-xs text-gray-400">JPG, PNG (maks. 2MB)</p>
                    </div>
                    <input type="file" name="foto_usaha" accept="image/jpeg,image/png" required class="hidden" onchange="previewFoto(this)">
                </label>
            </div>

            <!-- Pilih Lokasi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Usaha</label>

                <!-- Tab Switch -->
                <div class="flex rounded-lg border border-gray-200 overflow-hidden mb-3">
                    <button type="button" id="tab-map" onclick="switchTab('map')" class="flex-1 py-2 text-xs font-medium bg-orange-500 text-white transition">
                        📍 Pilih dari Peta
                    </button>
                    <button type="button" id="tab-manual" onclick="switchTab('manual')" class="flex-1 py-2 text-xs font-medium bg-white text-gray-600 transition">
                        ✏️ Input Koordinat
                    </button>
                </div>

                <!-- Opsi 1: Map -->
                <div id="section-map">
                    <div id="map" class="w-full h-52 rounded-lg border border-gray-200 z-0"></div>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-xs text-gray-400">Klik peta untuk menentukan lokasi</p>
                        <button type="button" onclick="goToMyLocation()" class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2.5 py-1 rounded-full transition flex items-center gap-1">
                            📍 Lokasi Saya
                        </button>
                    </div>
                </div>

                <!-- Opsi 2: Manual Koordinat -->
                <div id="section-manual" class="hidden">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                            <input type="text" id="manual-lat" placeholder="-6.5935" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" oninput="updateFromManual()">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                            <input type="text" id="manual-lng" placeholder="110.6741" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300" oninput="updateFromManual()">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Masukkan koordinat dari Google Maps atau GPS</p>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="latitude" id="input-lat" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="input-lng" value="{{ old('longitude') }}">

                <!-- Koordinat terpilih -->
                <div id="coords-display" class="mt-2 text-xs text-green-600 font-medium hidden">
                    ✓ Lokasi terpilih: <span id="coords-text"></span>
                </div>
            </div>

            <!-- Layanan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Layanan</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="layanan[]" value="tambal-ban" class="rounded"> Tambal Ban</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="layanan[]" value="isi-angin" class="rounded"> Isi Angin/Nitrogen</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="layanan[]" value="ganti-ban" class="rounded"> Ganti Ban</label>
                </div>
            </div>
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Buka</label>
                    <input type="time" name="jam_buka" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Tutup</label>
                    <input type="time" name="jam_tutup" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
            </div>

            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition">
                Daftar Mitra
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">Sudah punya akun? <a href="/login" class="text-orange-500 font-medium">Masuk</a></p>
    </div>

    <script>
        let map, marker;

        // Inisialisasi peta (Leaflet + OpenStreetMap - gratis)
        function initMap() {
            // Default: Jepara
            const defaultLat = -6.5935;
            const defaultLng = 110.6741;

            map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);

            marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            // Klik pada peta
            map.on('click', function(e) {
                setLocation(e.latlng.lat, e.latlng.lng);
            });

            // Drag marker
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                setLocation(pos.lat, pos.lng);
            });

            // Coba deteksi lokasi user
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    map.setView([lat, lng], 15);
                    setLocation(lat, lng);
                });
            }
        }

        function goToMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        map.setView([lat, lng], 17);
                        setLocation(lat, lng);
                    },
                    function() {
                        alert('Gagal mendeteksi lokasi. Pastikan GPS aktif.');
                    }
                );
            } else {
                alert('Browser tidak mendukung GPS.');
            }
        }

        function setLocation(lat, lng) {
            marker.setLatLng([lat, lng]);
            document.getElementById('input-lat').value = lat.toFixed(7);
            document.getElementById('input-lng').value = lng.toFixed(7);
            document.getElementById('manual-lat').value = lat.toFixed(7);
            document.getElementById('manual-lng').value = lng.toFixed(7);
            document.getElementById('coords-display').classList.remove('hidden');
            document.getElementById('coords-text').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);

            // Reverse geocoding - otomatis isi alamat dari koordinat
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
                .then(res => res.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('alamat').value = data.display_name;
                    }
                })
                .catch(() => {});
        }

        function updateFromManual() {
            const lat = parseFloat(document.getElementById('manual-lat').value);
            const lng = parseFloat(document.getElementById('manual-lng').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                setLocation(lat, lng);
                map.setView([lat, lng], 15);
            }
        }

        function switchTab(tab) {
            const tabMap = document.getElementById('tab-map');
            const tabManual = document.getElementById('tab-manual');
            const sectionMap = document.getElementById('section-map');
            const sectionManual = document.getElementById('section-manual');

            if (tab === 'map') {
                tabMap.className = 'flex-1 py-2 text-xs font-medium bg-orange-500 text-white transition';
                tabManual.className = 'flex-1 py-2 text-xs font-medium bg-white text-gray-600 transition';
                sectionMap.classList.remove('hidden');
                sectionManual.classList.add('hidden');
                // Refresh map size karena mungkin hidden sebelumnya
                setTimeout(() => map.invalidateSize(), 100);
            } else {
                tabManual.className = 'flex-1 py-2 text-xs font-medium bg-orange-500 text-white transition';
                tabMap.className = 'flex-1 py-2 text-xs font-medium bg-white text-gray-600 transition';
                sectionManual.classList.remove('hidden');
                sectionMap.classList.add('hidden');
            }
        }

        // Preview foto
        function previewFoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('foto-preview').src = e.target.result;
                    document.getElementById('foto-preview-container').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Init saat DOM ready
        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>
</html>
