<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use App\Models\TypeInteraction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;


/**
 * App/Models/Interaction
 * @property int $id
 * @property string $text
 * @property int $type_interaction_id
 * @property int $user_id
 * @property int $post_id
 * @mixin \Eloquent
 */
class Interaction extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['text', 'type_interaction_id', 'user_id', 'post_id', 'liked'];

    public function typeInteraction()
    {
        return $this->belongsTo(TypeInteraction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    protected $table = 'interactions';
}
