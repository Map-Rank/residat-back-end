<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\TopicResource;
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
            'content' => $this['content'],
            'images' => ImageResource::collection($this->whenLoaded('medias')),
            'creator' => UserResource::collection($this->whenLoaded('creator')),
            'topic' => TopicResource::make($this->whenLoaded('topic')),
            'liked' => $this->interactions->where('user_id', auth()->id())->where('type_interaction_id', 2)->first() != null   ,
            'like_count' => $this->likes()->count(),
            'comment_count' => $this->comments()->count(),
            'share_count' => $this->shares()->count(),
            'published_at' => $this['published_at'],
            'humanize_date_creation' => Carbon::parse($this['created_at'])->diffForHumans(),
            'created_at' => $this['created_at'],
            'likes' => UserResource::collection($this->whenLoaded('likes')),
            'comments' => CommentResource::collection($this->whenLoaded('postComments')),
            'shares' => UserResource::collection($this->whenLoaded('shares')),
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'sectors' => $this->sectors,

        ];
    }
}
