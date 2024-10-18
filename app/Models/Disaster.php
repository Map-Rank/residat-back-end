<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['description','locality','latitude','longitude','image','zone_id','level','type', 'end_period', 'start_period'];

    protected $dates = ['start_period', 'end_period'];

    public function zone (){
        return $this->belongsTo(Zone::class);
    }

    public function getStartPeriodAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }

    public function getEndPeriodAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }
}
