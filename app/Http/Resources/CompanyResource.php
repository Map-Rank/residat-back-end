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
        $appEnv = env('APP_ENV');
        $environments = ['local', 'dev', 'testing'];
        $url =  env('FRONT_URL').'/'.$this->profile;

        if (in_array($appEnv, $environments, true)) {
            $url = env('APP_URL').'/'.$this->profile;
        }

        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'owner_name' => $this->owner_name,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile' => $url, 
            'official_document' => $url,
            'zone_id' => $this->region_id,
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
