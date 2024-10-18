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
        $appEnv = env('APP_ENV');
        $environments = ['local', 'dev', 'testing'];
        $url =  env('FRONT_URL').$this['banner'];

        if (in_array($appEnv, $environments, true)) {
            $url = env('APP_URL').$this['banner'];
        }

        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'code' => $this['code'],
            'parent' => ZoneResource::make($this->whenLoaded('zone')),
            'parent_id' => $this['parent_id'],
            'level_id' => $this['level_id'],
            'latitude' => $this['latitude'],
            'longitude' => $this['longitude'],
            'geojson' => env('FRONT_URL').'/'.$this['geojson'],
            'banner' => $url,
            'created_at' => $this['created_at'],
            'vector' => VectorResource::make($this->whenLoaded('vector')),
        ];
    }
}
