<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the subscription.
     */
    public function view(User $user, Subscription $subscription): bool
    {
        return $user->id === $subscription->user_id;
    }

    /**
     * Determine whether the user can update the subscription.
     */
    public function update(User $user, Subscription $subscription): bool
    {
        return $user->id === $subscription->user_id &&
               $subscription->status !== 'cancelled';
    }

    /**
     * Determine whether the user can cancel the subscription.
     */
    public function cancel(User $user, Subscription $subscription): bool
    {
        return $user->id === $subscription->user_id &&
               $subscription->status === 'active';
    }

    /**
     * Determine whether the user can renew the subscription.
     */
    public function renew(User $user, Subscription $subscription): bool
    {
        return $user->id === $subscription->user_id &&
               $subscription->status === 'expired';
    }

    /**
     * Determine whether the user can create a payment for the subscription.
     */
    public function createPayment(User $user, Subscription $subscription): bool
    {
        return $user->id === $subscription->user_id;
    }
}
