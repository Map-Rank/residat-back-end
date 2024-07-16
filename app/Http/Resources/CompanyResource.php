<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ZoneResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'company_name' => $this->company_name,
            'owner_name' => $this->owner_name,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile' => env('FRONT_URL').'/'.$this->profile,
            'official_document' => $this->official_document,
            'zone_id' => $this->region_id,
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
