<?php

namespace App\Models;

use App\Models\User;
use App\Models\Zone;
use App\Models\Media;
use App\Models\Interaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['content', 'published_at', 'zone_id', 'user_id'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'interactions', 'post_id')
            ->withPivotValue('price', 'type_interaction_id', 'text');
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

    public function medias()
    {
        return $this->hasMany(Media::class);
    }
}
