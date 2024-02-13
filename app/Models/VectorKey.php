<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VectorKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['value', 'type', 'name', 'vector_id'];

    public function vector() : BelongsTo{
        return $this->belongsTo(Vector::class);
    }
}
