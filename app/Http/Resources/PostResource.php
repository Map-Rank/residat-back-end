<?php

namespace App\Http\Resources;

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
        //get if post is like or not
        $liked = $this->interactions->where('user_id', auth()->id())->first()?->liked ?? false;
        $liked = $liked === 1 ? true : false; // Si $liked est true, conservez-le, sinon, mettez-le à false

        return [
            'id' => $this['id'],
            'content' => $this['content'],
            'images' => ImageResource::collection($this->whenLoaded('medias')),
            'creator' => UserResource::collection($this->whenLoaded('creator')),
            'topic' => TopicResource::make($this->whenLoaded('topic')),
            'liked' => $liked,
            'like_count' => $this->likes()->count(),
            'comment_count' => $this->comments()->count(),
            'share_count' => $this->shares()->count(),
            'published_at' => $this['published_at'],
            'created_at' => $this['created_at'],
            'likes' => $this->likes,
            'comments' => CommentResource::collection($this->whenLoaded('postComments')),
            'shares' => $this->shares,
            'sectors' => $this->sectors,

        ];
    }
}
