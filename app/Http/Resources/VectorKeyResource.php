<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class VectorKeyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'value' => env('FRONT_URL').'/'.$this->value,
            'type' => $this->type,
            'name' => $this->name,
            'vector_id' => $this->vector_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
