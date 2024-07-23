<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
        $url = env('FRONT_URL') . '/' . $this['url'];

        if (in_array($appEnv, $environments, true)) {
            $url = env('APP_URL') . '/' . $this['url'];
        }
        
        return [
            'id' => $this['id'],
            'type' => $this['type'],
            'url' => $url,
        ];

    }
}
