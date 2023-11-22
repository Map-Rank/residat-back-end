<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'email' => 'required|email|unique:users|max:255',
                'phone' => 'required|string|max:20',
                'date_of_birth' => 'required|date',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'required|string|min:6',
                'gender' => 'required|in:male,female',
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
}