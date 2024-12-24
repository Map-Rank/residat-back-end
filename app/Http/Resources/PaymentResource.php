<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'amount' => $this->amount,
            'currency' => $this->currency,
            'transaction_id' => $this->transaction_id,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'payment_date' => $this->payment_date,
            'payment_details' => $this->payment_details,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
