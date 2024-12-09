<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Post;
use App\Models\Zone;
use App\Models\Event;
use App\Models\Interaction;
use App\Models\Notification;
use App\Models\UserSubscription;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\CustomVerificationNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'code',
        'date_of_birth',
        'phone',
        'avatar',
        'password',
        'active',
        'activated_at',
        'verified',
        'verified_at',
        'gender',
        'zone_id',
        'type',
        'profession',
        'description',
        'fcm_token',
        'language'
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
        'active' => 'boolean',
        'verified' => 'boolean'
    ];


    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'interactions', 'user_id')
            ->withPivotValue('type_interaction_id', 'text', 'created_at');
    }

    public function myPosts() {
        return $this->belongsToMany(Post::class,  'interactions', 'user_id')
            ->wherePivot('type_interaction_id', 1)->latest();
    }

    public function likes(){
        return $this->belongsToMany(Post::class,  'interactions', 'user_id')
            ->wherePivot('type_interaction_id', 2);
    }

    public function comments(){
        return $this->belongsToMany(Post::class,  'interactions', 'user_id')
            ->wherePivot('type_interaction_id', 3);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }


    public function likeInteractions()
    {
        return $this->hasMany(Interaction::class)
            ->where('type_interaction_id', 2);
    }


    public function commentInteractions()
    {
        return $this->hasMany(Interaction::class)
            ->where('type_interaction_id', 3);
    }

    public function shareInteractions()
    {
        return $this->hasMany(Interaction::class)
            ->where('type_interaction_id', 4);
    }


    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->belongsToMany(Subscription::class, 'user_subscription', 'user_id')
            ->wherePivotNull('end_at');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerificationNotification());
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function postCount()
    {
        return $this->hasMany(Interaction::class)
                    ->where('type_interaction_id', 1)
                    ->selectRaw('user_id, count(*) as count')
                    ->groupBy('user_id');
    }

    // Méthode pour obtenir la souscription active actuelle
    public function currentSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();
    }

}
