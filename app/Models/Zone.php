<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use App\Models\Level;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'parent_id', 'level_id'];

    public function parent()
    {
        return $this->belongsTo(Zone::class, 'parent_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function medias()
    {
        return $this->hasManyThrough(Media::class, Post::class);
    }
}
