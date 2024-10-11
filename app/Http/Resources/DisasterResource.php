<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisasterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'locality' => $this->locality,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'image' => $this->image,
            'zone_id' => $this->zone_id,
            'level' => $this->level,
            'type' => $this->type,
            'start_period' => $this->start_period,
            'end_period' => $this->end_period,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
