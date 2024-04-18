<?php

namespace App\Http\Resources;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use JsonSerializable;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'code' => $this->code,
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type,
            'address' => $this->address,
            'avatar' => $this->avatar,
            'profession' => $this->profession,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'zone_id' => $this->zone_id,
            'zone_id' => $this->zone_id,
            'role' => $this->roles,
            'active' => $this->active,
            'verified' => $this->verified,
            'activated_at' => $this->activated_at,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'zone' => ZoneResource::make($this->whenLoaded('zone')),

        ];
    }
}
