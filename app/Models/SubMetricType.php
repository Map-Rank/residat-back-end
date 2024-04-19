<?php

namespace App\Models;

use App\Models\SubMetricAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubMetricType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function metricType()
    {
        return $this->belongsTo(MetricType::class);
    }
}
