<?php

namespace App\Models;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

|
class Level extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
