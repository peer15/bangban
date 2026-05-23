<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DaftarMitraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'min:6', 'confirmed'],
            'alamat' => ['required', 'string'],
            'layanan' => ['required', 'array'],
            'foto_usaha' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'jam_buka' => ['nullable'],
            'jam_tutup' => ['nullable'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ];
    }
}
