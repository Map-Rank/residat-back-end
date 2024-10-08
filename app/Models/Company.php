<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'type',
        'description',
        'email',
        'phone',
        'profile',
        'zone_id',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
