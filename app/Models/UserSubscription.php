<?php

namespace App\Models;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSubscription extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['price', 'start_at', 'end_at', 'user_id', 'subscription_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
