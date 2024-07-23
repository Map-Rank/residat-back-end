<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class VectorKeyResource extends JsonResource
{
    public function toArray($request)
    {
        $appEnv = env('APP_ENV');
        $environments = ['local', 'dev', 'testing'];
        $url =  env('FRONT_URL').'/'.$this->value;

        if (in_array($appEnv, $environments, true)) {
            $url = env('APP_URL').'/'.$this->value;
        }

        return [
            'id' => $this->id,
            'value' => $url,
            'type' => $this->type,
            'name' => $this->name,
            'vector_id' => $this->vector_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
