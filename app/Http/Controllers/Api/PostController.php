<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use App\Models\Media;
use App\Models\Interaction;
use App\Service\UtilService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
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
            'zone_id'=> ['sometimes', 'integer', 'exists:zones,id'],
            'sectors'=> ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(),  __('bad params'), 400);
        }

        $validated = $validator->validated();

        $page = $validated['page'] ?? 0;
        $size = $validated['size'] ?? 10;

        $data = Post::with('creator', 'likes', 'comments', 'shares', 'medias');

        if(Auth::user() != null){
            $zone =  Auth::user()->loadMissing('zone.children')->zone;
            // Get all the descendants of the user's zone.
            if($zone != null){
                $descendants = collect();
                $descendants->push($zone);
                if ($zone->children != null){
                    $descendants =  UtilService::get_descendants($zone->children, $descendants);
                }
                $descendantIds = $descendants->pluck('id');
                $data = $data->whereIn('zone_id',  $descendantIds);
            }
        }

        if(isset($validated['zone_id'])){
            $data = $data->where('zone_id', $validated['zone_id']);
        }

        if(isset($validated['sectors'])){
            try{
                $sectorIds = json_decode($validated['sectors'], JSON_THROW_ON_ERROR);
                if(is_array($sectorIds)){
                    $data = $data->whereRelation('sectors', function(Builder  $b)use($sectorIds){
                        $b->whereIn('id', $sectorIds);
                    });
                }
            }catch(Exception $ex){
                Log::warning(sprintf('%s: The error is : %s', __METHOD__, $ex->getMessage()));
            }
        }

        $data =  $data->offSet($page * $size)->take($size)->latest()->get();

        return response()->success(PostResource::collection($data), __('Posts retrieved successfully'), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $post = Post::create($request->all());

        if ($request->hasFile('media')) {
            $mediaFiles = $request->file('media');
    
            $mediaPaths = [];
    
            foreach ($mediaFiles as $mediaFile) {
                $mediaPath = $mediaFile->store('media');
                $mediaPaths[] = [
                    'url' => Storage::url($mediaPath),
                    'type' => $mediaFile->getClientMimeType(),
                ];
            }
    
            $post->medias()->createMany($mediaPaths);
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

        // Mettez à jour les médias si de nouveaux fichiers sont fournis
        if ($request->hasFile('media')) {
            $mediaFiles = $request->file('media');

            $mediaPaths = [];

            foreach ($mediaFiles as $mediaFile) {
                $mediaPath = $mediaFile->store('media');
                $mediaPaths[] = [
                    'url' => Storage::url($mediaPath),
                    'type' => $mediaFile->getClientMimeType(),
                ];
            }

            // Supprimez les anciens médias associés au post
            $post->medias()->delete();

            // Créez les nouveaux médias associés au post
            $post->medias()->createMany($mediaPaths);
        }

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


    /**
     * Like the specified post.
     */
    public function like(string $id): JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $user = auth()->user();

        $interaction = new Interaction([
            'type_interaction_id' => 2,
            'user_id' => $user->id,
        ]);

        $post->interactions()->save($interaction);

        return response()->success($post, __('Post liked successfully'), 200);
    }

    /**
     * Comment on the specified post.
     */
    public function comment(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'text' => ['string','required'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(),  __('Insert comment'), 400);
        }

        $validated = $validator->validated();

        $commentText = $validated['text'];

        $post = Post::find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $user = auth()->user();

        $interaction = new Interaction([
            'text' => $commentText, 
            'type_interaction_id' => 3,
            'user_id' => $user->id,
        ]);

        $post->interactions()->save($interaction);

        return response()->success($post, __('Comment added successfully'), 200);
    }

    /**
     * Share the specified post.
     */
    public function share(string $id): JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $user = auth()->user();

        $interaction = new Interaction([
            'type_interaction_id' => 4,
            'user_id' => $user->id,
        ]);

        $post->interactions()->save($interaction);

        return response()->success($post, __('Post shared successfully'), 200);
    }
}
