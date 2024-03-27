<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'desscription', 'Location', 'Organized_by', 'user_id', 'published_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
