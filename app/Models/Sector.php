<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function posts() : BelongsToMany {
        return $this->belongsToMany(Post::class, 'sector_post', 'sector_id');
    }
}
