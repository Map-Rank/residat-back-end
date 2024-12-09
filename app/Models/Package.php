<?php

namespace App\Models;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fr',
        'name_en',
        'level', 
        'price', 
        'description_fr', 
        'description_en', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
