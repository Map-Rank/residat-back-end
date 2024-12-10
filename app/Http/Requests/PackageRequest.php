<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
        return [
            'name_fr' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'level' => [
                'required', 
                Rule::in(['National', 'Regional', 'Divisional', 'Subdivisional'])
            ],
            'periodicity' => [
                'required', 
                Rule::in(['Month', 'Quarter', 'Half', 'Annual'])
            ],
            'price' => ['required', 'integer', 'min:0'],
            'description_fr' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'is_active' => ['boolean']
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The package name is required.',
            'level.in' => 'Invalid package level selected.',
            'price.required' => 'The price is required.',
            'price.integer' => 'The price must be a number.',
            'price.min' => 'The price must be a positive number.'
        ];
    }
}
