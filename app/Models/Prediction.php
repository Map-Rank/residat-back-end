<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prediction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'zone_id',
        'date',
        'd1_risk',
        'd2_risk',
        'd3_risk',
        'd4_risk',
        'd5_risk'
    ];

    protected $casts = [
        'date' => 'date',
        'd1_risk' => 'array',
        'd2_risk' => 'array',
        'd3_risk' => 'array',
        'd4_risk' => 'array',
        'd5_risk' => 'array'
    ];

    /**
     * Get the zone that owns the prediction.
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Scope a query to filter predictions by zone and date.
     */
    public function scopeByZoneAndDate($query, $zoneId, $date)
    {
        return $query->where('zone_id', $zoneId)
                    ->where('date', $date);
    }
}
