<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['report_id', 'value', 'metric_type_id'];

    public function metricType(): BelongsTo{
        return $this->belongsTo(MetricType::class);
    }

    public function report(): BelongsTo{
        return $this->belongsTo(Report::class);
    }
}
