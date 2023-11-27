<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'page' => ['sometimes','numeric'],
            'size'=> ['sometimes', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(),  __('bad params'), 400);
        }

        $validated = $validator->validated();

        $page = $validated['page'] ?? 0;
        $size = $validated['size'] ?? 10;
        

        $posts = Post::with('creator', 'likes', 'comments', 'shares')
            ->offSet($page * $size)->take($size)
            ->latest()
            ->get();

        return response()->success(PostResource::collection($posts), __('Posts retrieved successfully'), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $post = Post::create($request->all());
        // Ajout du média
        if ($request->hasFile('media')) {
            $mediaFile = $request->file('media');
            $mediaPath = $mediaFile->store('media'); // Le dossier 'media' peut être ajusté selon votre structure
            $mediaType = $mediaFile->getClientMimeType();

            // Création du média associé au post
            $media = Media::create([
                'url' => Storage::url($mediaPath),
                'type' => $mediaType,
                'post_id' => $post->id,
            ]);

            // Vous pouvez également lier le média au post via la relation
            $post->media()->save($media);
        }
        return response()->success($post, __('Post created successfully'), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        return response()->success($post, __('Post retrieved successfully'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $post->update($request->all());

        return response()->success($post, __('Post updated successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $post->delete();

        return response()->success([], __('Post deleted successfully'), 200);
    }
}
