<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authUser = auth()->user();
        $appEnv = env('APP_ENV');
        $environments = ['local', 'dev', 'testing'];
        $url =  env('FRONT_URL').''.$this->avatar;

        if (in_array($appEnv, $environments, true)) {
            $url = env('APP_URL'). '/storage/' .$this->avatar;
        }

        return [
            'id' => $this['id'],
            'first_name' => $this['first_name'],
            'last_name' => $this['last_name'],
            'code' => $this['code'],
            'email' => $this['email'],
            'code' => $this['code'],
            'date_of_birth' => $this['date_of_birth'],
            'phone' => $this['phone'],
            'address' => $this['address'],
            'profession' => $this['profession'],
            'description' => $this['description'],
            'language' => $this['language'],
            'active' => $this['active'],
            'verified' => $this['verified'],
            'activated_at' => $this['activated_at'],
            'verified_at' => $this['verified_at'],
            'gender' => $this['gender'],
            'type' => $this['type'],
            'fcm_token' => $this['fcm_token'],
            'avatar' => $url,
            'liked' => $authUser && $this->interactions->where('user_id', $authUser->id)->where('type_interaction_id', 2)->first() != null,
            'activeSubscription' => SubscriptionResource::collection($this->whenLoaded('activeSubscription')),
            'my_posts' => PostResource::collection($this->whenLoaded('myPosts')),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'my_likes' => InteractionResource::collection($this->whenLoaded('likeInteractions')),
            'my_shares' => InteractionResource::collection($this->whenLoaded('shareInteractions')),
            'my_comments' => InteractionResource::collection($this->whenLoaded('commentInteractions')),
            'follower_count' => $this->followers_count,
            'following_count' => $this->following_count,
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'events' => $this['events'],
            'created_at' => $this['created_at'],
        ];
    }
}
