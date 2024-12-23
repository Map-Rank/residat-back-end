<?php

namespace App\Models;

use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App/Models/Subscription
 * @property int $id
 * @property string $name
 * @property string $periodicity
 * @property double $price
 * @mixin \Eloquent
 */

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'package_id', 
        'zone_id', 
        'start_date', 
        'end_date', 
        'status', 
        'notes'
    ];

    protected $dates = [
        'start_date', 
        'end_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Relation avec la zone
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    // Relation avec les paiements
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
