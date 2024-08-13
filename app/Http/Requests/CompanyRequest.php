<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
        $companyId = $this->route('company') ? $this->route('company')->id : null;

        return [
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'required|email|unique:companies,email,' . $companyId,
            'phone' => 'required|string|max:255',
            'profile_picture' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
            'zone_id' => 'required|integer|exists:zones,id',
        ];
    }
}
