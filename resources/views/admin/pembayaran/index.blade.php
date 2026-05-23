@extends('layouts.admin')
@section('title', 'Pembayaran')

@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6">Pembayaran Mitra</h2>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Mitra</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Jenis</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Jumlah</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Periode</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($pembayarans as $p)
            <tr>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $p->mitra->user->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ ucfirst($p->jenis) }}</td>
                <td class="px-4 py-3 font-medium">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-1 rounded-full {{ match($p->status) {
                        'lunas' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'gagal' => 'bg-red-100 text-red-700',
                    } }}">{{ ucfirst($p->status) }}</span>
                </td>
                <td class="px-4 py-3 text-gray-500">
                    {{ $p->periode_mulai?->format('d/m/Y') }} - {{ $p->periode_selesai?->format('d/m/Y') }}
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $p->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pembayarans->links() }}</div>
@endsection
