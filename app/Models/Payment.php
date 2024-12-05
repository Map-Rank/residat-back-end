<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id', 
        'amount', 
        'currency', 
        'transaction_id', 
        'payment_method', 
        'status', 
        'payment_date', 
        'payment_details'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

    // Relation avec la souscription
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
