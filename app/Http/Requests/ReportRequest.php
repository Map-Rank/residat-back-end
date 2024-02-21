<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'description' => 'required|string',
            'type' => 'required|string',
            'image' => 'nullable|image|mimes:svg,jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'vector' => 'required|file',
            'vector.path' => 'required|string',
            'vector.category' => 'required|in:MAP,WATER_STRESS,DROUGHT,FLOOD',
            'vector.type' => 'required|in:IMAGE,SVG',
            'vector_keys.*.value' => 'required|string',
            'vector_keys.*.type' => 'required|in:COLOR,IMAGE,FIGURE',
            'vector_keys.*.name' => 'required|string',
            'report_items.*.metric_type_id' => 'required|exists:metric_types,id',
            'report_items.*.value' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'end_date.after' => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}
