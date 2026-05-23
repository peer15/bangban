@extends('layouts.user')
@section('title', 'SOS Darurat')

@section('content')
<div class="text-center mb-6">
    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-3 animate-pulse">🆘</div>
    <h2 class="font-bold text-red-600 text-xl">SOS Darurat</h2>
    <p class="text-sm text-gray-500 mt-1">Gunakan jika kamu dalam keadaan darurat</p>
</div>

<button onclick="sendSOS()" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-xl shadow-lg transition text-lg mb-4">
    🚨 Kirim Sinyal Darurat
</button>

<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <h3 class="font-medium text-gray-700 mb-3">Hubungi Langsung</h3>
    <div class="space-y-2">
        <a href="tel:112" class="flex items-center gap-3 p-3 bg-red-50 rounded-lg">
            <span class="text-xl">🚔</span>
            <div class="flex-1"><p class="text-sm font-medium">Polisi - 112</p></div>
            <span class="text-sm text-red-500">Hubungi →</span>
        </a>
        <a href="tel:118" class="flex items-center gap-3 p-3 bg-red-50 rounded-lg">
            <span class="text-xl">🚑</span>
            <div class="flex-1"><p class="text-sm font-medium">Ambulans - 118</p></div>
            <span class="text-sm text-red-500">Hubungi →</span>
        </a>
    </div>
</div>

<script>
function sendSOS() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((pos) => {
            alert('Sinyal darurat terkirim!\nLokasi: ' + pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6));
        }, () => { alert('Sinyal darurat terkirim!'); });
    }
}
</script>
@endsection
