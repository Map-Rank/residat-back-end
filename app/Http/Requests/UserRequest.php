<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'date_of_birth' => 'required|date',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'nullable|string|min:6',
                'gender' => 'nullable|in:male,female',
                'zone_id' => 'exists:zones,id',
                ];
        }
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:male,female',
            'zone_id' => 'exists:zones,id',
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function bodyParameters()
    {
        return [
            'first_name' => [
                'description' => 'first Name',
                'example' => 'Joe'
            ],
            'last_name' => [
                'description' => 'last name',
                'example' => 'Bernard'
            ],
            'email' => [
                'description' => 'email',
                'example' => 'tests@example.com'
            ],
            'phone' => [
                'description' => 'phone number',
                'example' => '002376698803159'
            ],
            'date_of_birth' => [
                'description' => 'date of birth',
                'example' => Carbon::now()
            ],
            'avatar' => [
                'description' => 'profil picture',
                'example' => 'image.jpg'
            ],
            'password' => [
                'description' => 'password',
                'example' => 'password!'
            ],
            'gender' => [
                'description' => 'gender',
                'example' => 'Male'
            ],
            'zone_id' => [
                'description' => 'id of zone',
                'example' => 1
            ]
            
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