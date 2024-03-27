<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Peut être ajusté selon vos besoins d'autorisation
    }

    public function rules()
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'organized_by' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'published_at' => 'required|date',
        ];
    }
}