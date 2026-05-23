@extends('layouts.admin')
@section('title', 'Pengguna')

@section('content')
<h2 class="text-xl font-semibold text-gray-800 mb-6">Daftar Pengguna</h2>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Telepon</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Bergabung</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($users as $user)
            <tr>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $user->phone ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
