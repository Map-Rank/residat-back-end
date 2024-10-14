<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DisasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|string',
            'locality' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|string',
            'zone_id' => 'required|exists:zones,id',
            'level' => 'required|integer|min:1',
            'type' => 'required|in:FLOOD,DROUGHT',
            'start_period' => 'nullable|date',
            'end_period' => 'nullable|date|after_or_equal:start_period',
        ];
    }

    /**
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->errors($validator->errors(), 'Validation errors', 422));
    }
}
