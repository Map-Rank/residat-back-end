<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'status' => ['sometimes', Rule::in(['active', 'expired', 'cancelled'])],
                'notes' => ['nullable', 'string', 'max:500']
            ];
        }

        return [
            'package_id' => ['required', 'exists:packages,id'],
            'zone_id' => ['required', 'exists:zones,id'],
            'amount' => ['required', 'numeric', 'min:0'], // Added amount validation
            'payment_method' => ['required', 'string'],
            'payment_details' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:500']
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'package_id.exists' => 'Le package sélectionné est invalide.',
            'zone_id.exists' => 'La zone sélectionnée est invalide.'
        ];
    }
}
