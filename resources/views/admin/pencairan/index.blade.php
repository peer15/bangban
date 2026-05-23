@extends('layouts.admin')
@section('title', 'Pencairan Saldo')

@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6">💸 Pencairan Saldo Mitra</h2>

@if($pencairanPending->isNotEmpty())
<div class="mb-6">
    <h3 class="font-medium text-yellow-700 mb-3">⏳ Menunggu Transfer ({{ $pencairanPending->count() }})</h3>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Mitra</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Jumlah</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal Request</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pencairanPending as $p)
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $p->mitra->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $p->mitra->user->phone }}</p>
                        @if($p->mitra->jenis_rekening)
                            <p class="text-xs text-blue-600 mt-1">{{ $p->mitra->jenis_rekening }} • {{ $p->mitra->nomor_rekening }} • a.n {{ $p->mitra->nama_rekening }}</p>
                        @else
                            <p class="text-xs text-red-500 mt-1">⚠️ Belum isi rekening</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-orange-600">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $p->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="/admin/pencairan/{{ $p->id }}/selesai" onsubmit="return confirm('Tandai sudah ditransfer?')">
                            @csrf
                            <button class="text-sm bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg transition">✓ Sudah Transfer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<h3 class="font-medium text-gray-700 mb-3">Riwayat Pencairan</h3>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Mitra</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Jumlah</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($semuaPencairan as $p)
            <tr>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $p->mitra->user->name }}</td>
                <td class="px-4 py-3">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-1 rounded-full {{ $p->status === 'selesai' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $p->status === 'selesai' ? 'Ditransfer' : 'Pending' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $p->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada pencairan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $semuaPencairan->links() }}</div>
@endsection
