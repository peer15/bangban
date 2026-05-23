<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'layanan' => ['required', 'in:tambal-ban,isi-angin,ganti-ban'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'catatan_lokasi' => ['nullable', 'string', 'max:500'],
            'pembayaran' => ['required', 'in:tunai,ewallet'],
        ];
    }
}
