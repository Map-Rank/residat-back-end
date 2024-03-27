<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->Location,
            'organized_by' => $this->Organized_by,
            'user_id' => $this->user_id,
            'published_at' => $this->published_at,
            'is_valid' => $this->is_valid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}