<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $appEnv = env('APP_ENV');
        $environments = ['local', 'dev', 'testing'];
        $url =  env('FRONT_URL').'/'.$this->file;

        if (in_array($appEnv, $environments, true)) {
            $url = env('APP_URL'). '/storage/' .$this->file;
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'text' => $this->text,
            'page_link' => $this->page_link,
            'rating' => $this->rating,
            'file' => $url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
