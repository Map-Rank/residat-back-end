<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class WeatherPredictions extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['zone_id', 'path', 'date'];

    public function zone() : BelongsTo {
        return $this->belongsTo(Zone::class);
    }

}
