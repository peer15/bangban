@extends('layouts.admin')
@section('title', 'Pesanan')

@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6">Semua Pesanan</h2>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">#</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Pengguna</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Layanan</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Mitra</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Total</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($pesanans as $pesanan)
            <tr>
                <td class="px-4 py-3 text-gray-500">{{ $pesanan->id }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $pesanan->user->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $pesanan->nama_layanan }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $pesanan->mitra?->user?->name ?? '-' }}</td>
                <td class="px-4 py-3 font-medium">Rp {{ number_format($pesanan->total_biaya, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-1 rounded-full {{ match($pesanan->status) {
                        'selesai' => 'bg-green-100 text-green-700',
                        'dibatalkan' => 'bg-red-100 text-red-700',
                        default => 'bg-orange-100 text-orange-700',
                    } }}">{{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}</span>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $pesanan->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pesanans->links() }}</div>
@endsection
