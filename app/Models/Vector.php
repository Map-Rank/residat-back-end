<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vector extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['path', 'model_id', 'category', 'type', 'model_type'];

    public function zone() : BelongsTo{
        return $this->belongsTo(Zone::class, 'model_id', 'id')->where('model_type', 'App\Models\Zone');
    }

    public function report() : BelongsTo{
        return $this->belongsTo(Report::class, 'model_id', 'id')->where('model_type', 'App\Models\Report');
    }

    public function keys() : HasMany{
        return $this->hasMany(VectorKey::class);
    }
}
