<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer tous les posts avec leurs relations et compter les interactions
        $posts = Post::with('creator', 'likes', 'comments', 'shares', 'medias')
            ->withCount('likes', 'comments', 'shares')
            ->latest()
            ->paginate(10);

        // Calculer le total des interactions pour chaque post
        foreach ($posts as $post) {
            $post->total_interactions = $post->likes_count + $post->comments_count + $post->shares_count;
        }

        // Passer les données à la vue
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        // Création d'un nouveau post
        $post = Post::create($request->all());

        return redirect()->route('posts.index')->with('success', 'Post créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id)->loadMissing('postComments', 'creator', 'sectors');
        return view('posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Allow a post
     */
    public function allowPost($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return redirect()->back()->with('error', 'Post not found');
        }

        // Inversez directement la valeur du booléen
        $post->update([
            'active' => !$post->active,
        ]);

        $message = $post->active ? 'Post activated successfully' : 'Post deactivated successfully';

        return redirect()->route('post.detail', ['id' => $post->id])->with('success', $message);
    }
}
