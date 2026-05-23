<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMitraProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_usaha' => ['nullable', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'layanan' => ['required', 'array'],
            'jam_buka' => ['nullable'],
            'jam_tutup' => ['nullable'],
            'foto_usaha' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'jenis_rekening' => ['nullable', 'string', 'max:50'],
            'nomor_rekening' => ['nullable', 'string', 'max:50'],
            'nama_rekening' => ['nullable', 'string', 'max:255'],
        ];
    }
}
