<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App/Models/Media
 * @property int $id
 * @property string $url
 * @property string $type
 * @property int $post_id
 * @mixin \Eloquent
 */
class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['url', 'type', 'post_id'];

    public function post()
    {
        $level = new Level();

        return $this->belongsTo(Post::class);
    }

    protected $table = 'medias';
}
