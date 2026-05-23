<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bintang' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
