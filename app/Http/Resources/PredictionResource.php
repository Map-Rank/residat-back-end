<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ZoneResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PredictionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'zone_id' => $this->zone_id,
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'date' => $this->date->toDateString(),
            'd1_risk' => $this->d1_risk,
            'd2_risk' => $this->d2_risk,
            'd3_risk' => $this->d3_risk,
            'd4_risk' => $this->d4_risk,
            'd5_risk' => $this->d5_risk,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
