<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostRequest extends FormRequest
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
            'content' => 'required',
            'published_at' => 'nullable|date',
            'zone_id' => 'required|exists:zones,id',
            'media' => 'nullable',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,mp4,mov,avi,wmv,mp3|max:2048', // Ajoute la règle pour le media
            'sectors' => 'nullable',
        ];
    }

    /**
     * @codeCoverageIgnore
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->errors($validator->errors(), 'Validation errors', 422));
    }
    
    /**
     * @codeCoverageIgnore
     */
    public function bodyParameters()
    {
        return [
            'content' => [
                'description' => 'Content of the post',
                'example' => 'New Post'
            ],
            'published_at' => [
                'description' => 'date publication of post',
                'example' => Carbon::now()
            ],
            'zone_id' => [
                'description' => 'id of concern zone of post',
                'example' => 1
            ],
            'sectors' => [
                'description' => 'Sector of post',
                'example' => 1
            ],
            'media' => [
                'description' => 'Images of the post (jpeg,png,jpg,gif,pdf,mp4,mov,avi,wmv,mp3)',
                'example' => 'path/image.jpg'
            ],
            
        ];
    }
}
