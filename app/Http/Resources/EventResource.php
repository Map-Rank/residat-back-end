<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Http\Resources\ZoneResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'organized_by' => $this->organized_by,
            'user_id' => $this->user_id,
            'published_at' => $this->published_at,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'image' => env('APP_URL').'/'.$this->media,
            'humanize_date_creation' => Carbon::parse($this->created_at)->diffForHumans(),
            'sector' => $this->sector,
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'is_valid' => $this->is_valid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
