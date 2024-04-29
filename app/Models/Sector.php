<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sector extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function posts() : BelongsToMany {
        return $this->belongsToMany(Post::class, 'sector_post', 'sector_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
