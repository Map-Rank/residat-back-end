<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

    class Report extends Model
    {
        use HasFactory, SoftDeletes;

        protected $fillable = ['code', 'user_id', 'zone_id', 'description', 'type', 'image', 'start_date', 'end_date'];

        public function zone() : BelongsTo{
            return $this->belongsTo(Zone::class);
        }

        public function creator() : BelongsTo{
            return $this->belongsTo(User::class, 'user_id', 'id');
        }

        public function items() : HasMany{
            return $this->hasMany(ReportItem::class, 'report_id', 'id');
        }

        public function vector() {
            return $this->hasOne(Vector::class, 'model_id')->where('model_type', self::class);
        }
    }
