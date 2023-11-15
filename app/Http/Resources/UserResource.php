<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this['first_name'],
            'last_name' => $this['last_name'],
            'code' => $this['code'],
            'email' => $this['email'],
            'code' => $this['code'],
            'date_of_birth' => $this['date_of_birth'],
            'phone' => $this['phone'],
            'address' => $this['address'],
            'avatar' => $this['avatar'],
            'active' => $this['active'],
            'verified' => $this['verified'],
            'activated_at' => $this['activated_at'],
            'verified_at' => $this['verified_at'],
            'gender' => $this['gender'],
        ];
    }
}
