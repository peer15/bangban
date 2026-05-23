@extends('layouts.user')
@section('title', 'Beri Rating')

@section('content')
<div class="text-center mb-6">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">✅</div>
    <h2 class="font-semibold text-gray-800 text-lg">Layanan Selesai!</h2>
    <p class="text-sm text-gray-500">Beri rating untuk {{ $pesanan->mitra->user->name }}</p>
</div>

<form method="POST" action="/pesanan/{{ $pesanan->id }}/rating" class="bg-white rounded-xl shadow-sm p-4">
    @csrf
    <div class="flex justify-center gap-2 mb-4" id="star-rating">
        @for($i = 1; $i <= 5; $i++)
            <button type="button" onclick="setRating({{ $i }})" class="text-3xl star text-gray-300 hover:text-yellow-400 transition">★</button>
        @endfor
    </div>
    <input type="hidden" name="bintang" id="input-bintang" value="5">

    <textarea name="ulasan" placeholder="Tulis ulasan (opsional)..." class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 h-24 resize-none focus:outline-none focus:ring-2 focus:ring-orange-300"></textarea>

    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl mt-3 transition">
        Kirim Rating
    </button>
</form>

<script>
let currentRating = 5;
function setRating(n) {
    currentRating = n;
    document.getElementById('input-bintang').value = n;
    document.querySelectorAll('.star').forEach((s, i) => {
        s.classList.toggle('text-yellow-400', i < n);
        s.classList.toggle('text-gray-300', i >= n);
    });
}
setRating(5);
</script>
@endsection
