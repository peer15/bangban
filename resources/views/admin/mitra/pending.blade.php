@extends('layouts.admin')
@section('title', 'Mitra Pending')

@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6">⏳ Menunggu Verifikasi</h2>

@if($mitras->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">Tidak ada mitra yang menunggu verifikasi.</div>
@else
    <div class="space-y-4">
        @foreach($mitras as $mitra)
            @include('admin.mitra._card', ['mitra' => $mitra])
        @endforeach
    </div>
    <div class="mt-4">{{ $mitras->links() }}</div>
@endif

<!-- Modal Foto -->
<div id="foto-modal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4" onclick="closeFoto()">
    <img id="foto-modal-img" src="" class="max-w-full max-h-[85vh] rounded-xl shadow-2xl" alt="Foto usaha">
</div>

<script>
function openFoto(src) {
    document.getElementById('foto-modal-img').src = src;
    document.getElementById('foto-modal').classList.remove('hidden');
    document.getElementById('foto-modal').classList.add('flex');
}
function closeFoto() {
    document.getElementById('foto-modal').classList.add('hidden');
    document.getElementById('foto-modal').classList.remove('flex');
}
</script>
@endsection
