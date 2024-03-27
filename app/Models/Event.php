<?php

namespace App\Models;

use App\Models\User;
use App\Models\Zone;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'desscription', 'Location', 'date_debut','date_fin', 'Organized_by', 'user_id', 'published_at', 'is_valid','media'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
