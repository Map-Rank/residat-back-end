<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App/Models/Topic
 * @property int $id
 * @property string $name
 * @mixin \Eloquent
 */
class Topic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

}
