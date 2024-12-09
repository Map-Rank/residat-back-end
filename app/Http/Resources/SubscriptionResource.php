<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\PackageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'package' => new PackageResource($this->whenLoaded('package')),
            'zone' => $this->whenLoaded('zone'),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'notes' => $this->notes,
            'duration' => $this->calculateDuration(),
            'is_active' => $this->isActive(),
            'days_remaining' => $this->calculateDaysRemaining(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    /**
     * Calculate subscription duration in days.
     *
     * @return int
     */
    private function calculateDuration(): int
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        
        return $startDate->diffInDays($endDate);
    }

    /**
     * Check if subscription is currently active.
     *
     * @return bool
     */
    private function isActive(): bool
    {
        return $this->status === 'active' && 
               now()->between(
                   Carbon::parse($this->start_date), 
                   Carbon::parse($this->end_date)
               );
    }

    /**
     * Calculate remaining days for the subscription.
     *
     * @return int|null
     */
    private function calculateDaysRemaining(): ?int
    {
        if (!$this->isActive()) {
            return null;
        }

        return now()->diffInDays(Carbon::parse($this->end_date));
    }
}
