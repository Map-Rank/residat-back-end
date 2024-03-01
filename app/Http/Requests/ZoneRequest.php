<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ZoneRequest extends FormRequest
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
                'name' => ['sometimes','string'],
                'parent_id' => ['sometimes','exists:zones,id',],
            ];
        }
        return [
            'name' => ['required','string'],
            'division_id' => ['sometimes','exists:zones,id',],
            'region_id' => ['sometimes','exists:zones,id',],
            'level_id' => ['sometimes','exists:levels,id',],
            'data' => 'nullable|image|mimes:svg,jpeg,png,jpg,gif|max:2048',
            'vector_keys' => 'array',
            'vector_keys.*.value' => 'required|string',
            'vector_keys.*.name' => 'required|string',
            'vector_keys.*.type' => 'required|string',
            
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
