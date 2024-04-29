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
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
            'sector_id' => 'required|exists:sectors,id',
            'zone_id' => 'required|exists:zones,id',
            'media' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,mp4,mov,avi,wmv,mp3|max:2048', // Ajoute la règle pour le media
        ];
    }
}