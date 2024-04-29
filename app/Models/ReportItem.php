<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['report_id', 'value', 'sub_metric_type_id', 'description'];

    public function subMetricType(): BelongsTo{
        return $this->belongsTo(SubMetricType::class);
    }

    public function report(): BelongsTo{
        return $this->belongsTo(Report::class);
    }
}
