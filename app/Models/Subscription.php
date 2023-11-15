<?php

namespace App\Models;

use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App/Models/Subscription
 * @property int $id
 * @property string $name
 * @property string $periodicity
 * @property double $price
 * @mixin \Eloquent
 */

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'periodicity', 'price'];

    public function subscription()
    {
        return $this->belongsToMany(User::class, 'user_subscription', 'subscription_id');
    }
}
