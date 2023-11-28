<?php

namespace App\Models;

use App\Models\User;
use App\Models\Zone;
use App\Models\Media;
use App\Models\Interaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App/Models/Post
 * @property int $id
 * @property string $content
 * @property datetime $published_at
 * @property int $zone_id
 * @mixin \Eloquent
 */

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['content', 'published_at', 'zone_id', 'sector_id'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function users(){
        return $this->belongsToMany(User::class,  'interactions', 'post_id');
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

    public function shares(){
        return $this->belongsToMany(User::class,  'interactions', 'post_id')
            ->wherePivot('type_interaction_id', 4);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    public function sectors() : BelongsToMany{
        return $this->belongsToMany(Sector::class, 'sector_post', 'post_id');
    }
}
