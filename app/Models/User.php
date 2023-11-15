<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Post;
use App\Models\Zone;
use App\Models\Interaction;
use App\Models\UserSubscription;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * App/Models/User
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $avatar
 * @property enum $gender
 * @property boolean $active
 * @property boolean $verified
 * @property datetime $verified_at
 * @property datetime $activated_at
 * @mixin \Eloquent
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;


    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'interactions', 'user_id')
            ->withPivotValue('type_interaction_id', 'text', 'created_at');
    }

    public function creator() {
        return $this->belongsToMany(User::class,  'interactions', 'post_id')
            ->wherePivot('type_interaction_id', 1);
    }

    public function likes(){
        return $this->belongsToMany(User::class,  'interactions', 'post_id')
            ->wherePivot('type_interaction_id', 2);
    }

    public function comments(){
        return $this->belongsToMany(User::class,  'interactions', 'post_id')
            ->wherePivot('type_interaction_id', 3);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'user_subscription', 'user_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'avatar',
        'password',
        'active',
        'activated_at',
        'verified',
        'verified_at',
        'gender',
        'zone_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
