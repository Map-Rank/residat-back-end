<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'text' => $this['text'],
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'creator' => UserResource::make($this->creator()),
            'topic' => TopicResource::make($this->whenLoaded('topic')),
            'like_count' => $this->likes()->count(),
            'comment_count' => $this->comments()->count(),
            'share_count' => $this->shares()->count(),
            'published_at' => $this['published_at'],
            'created_at' => $this['created_at'],
        ];
    }
}