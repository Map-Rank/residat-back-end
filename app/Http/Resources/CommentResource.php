<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'text' => $this->text,
            'user' => UserResource::make($this->whenLoaded('user')),
            'humanize_date_creation' => Carbon::parse($this['created_at'])->diffForHumans(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
