<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectorPost extends Pivot
{
    use HasFactory, SoftDeletes;

    public function post() : BelongsTo{
        return $this->belongsTo(Post::class);
    }

    public function sector() : BelongsTo{
        return $this->belongsTo(Sector::class);
    }

    protected $table = 'sector_post';
}
