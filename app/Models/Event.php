<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'desscription', 'Location', 'date_debut','date_fin', 'Organized_by', 'user_id', 'published_at', 'is_valid'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
