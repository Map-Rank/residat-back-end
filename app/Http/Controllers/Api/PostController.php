<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $posts = Post::with('creator','likes', 'comments', 'shares')->withCount('likes', 'comments', 'shares')->latest()->paginate(10);
        return response()->success($posts, __('Posts retrieved successfully'), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $post = Post::create($request->all());
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
