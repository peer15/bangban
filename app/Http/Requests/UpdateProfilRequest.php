<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $this->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'min:6', 'confirmed'],
        ];
    }
}
