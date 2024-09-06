<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
            'titre_en' => 'required|string|max:255',
            'titre_fr' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'content_en' => 'required|string',
            'content_fr' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'titre_en.required' => 'The English title is required.',
            'titre_fr.required' => 'The French title is required.',
            'zone_id.required' => 'The Zone ID is required.',
            'zone_id.exists' => 'The selected Zone ID is invalid.',
            'content_en.required' => 'The English content is required.',
            'content_fr.required' => 'The French content is required.',
        ];
    }
}
