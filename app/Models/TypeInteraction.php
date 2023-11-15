<?php

namespace App\Models;

use App\Models\Interaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App/Models/TypeInteraction
 * @property int $id
 * @property string $name
 * @mixin \Eloquent
 */

class TypeInteraction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }
}
