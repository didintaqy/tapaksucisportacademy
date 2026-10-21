<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'relation' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'max:2000'],

            // Honeypot: harus kosong. Bot yang mengisi field ini akan ditolak.
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rating.between' => 'Rating harus antara 1 sampai 5.',
            // Honeypot: jangan ungkap alasan penolakan yang sebenarnya.
            'website.max' => 'Testimoni tidak dapat diproses.',
        ];
    }
}
