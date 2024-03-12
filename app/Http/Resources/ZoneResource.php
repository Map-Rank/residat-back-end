<?php

namespace App\Http\Resources;

use App\Models\Zone;
use Illuminate\Http\Request;
use App\Http\Resources\VectorResource;
use App\Http\Resources\VectorKeyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'parent' => ZoneResource::make($this->whenLoaded('zone')),
            'banner' => $this['banner'],
            'created_at' => $this['created_at'],
            'vector' => VectorResource::make($this->whenLoaded('vector')),
        ];
    }
}
