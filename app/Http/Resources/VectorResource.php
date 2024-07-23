<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VectorResource extends JsonResource
{
    public function toArray($request)
    {
        $appEnv = env('APP_ENV');
        $environments = ['local', 'dev', 'testing'];
        $url =  env('FRONT_URL').$this->path;

        if (in_array($appEnv, $environments, true)) {
            $url = env('APP_URL').$this->path;
        }

        return [
            'id' => $this->id,
            'path' => $url,
            'model_id' => $this->model_id,
            'category' => $this->category,
            'type' => $this->type,
            'model_type' => $this->model_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'keys' => VectorKeyResource::collection($this->whenLoaded('vectorKeys'))
        ];
    }
}
