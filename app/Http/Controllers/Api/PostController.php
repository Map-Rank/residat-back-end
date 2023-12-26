<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use App\Models\Media;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\InteractionResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserFullResource;
use App\Models\TypeInteraction;
use App\Models\User;
use App\Service\UtilService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @group Module Posts
 */
class PostController extends Controller
{
    /**
     * Get all posts
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

        $data = Post::with('creator', 'likes', 'comments', 'shares', 'medias', 'postComments');

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
                    $data = $data->whereRelation('sectors', function($b)use($sectorIds){
                        $b->whereIn('sectors.id', $sectorIds);
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
     * Store the specified post
     */
    public function store(PostRequest $request)
    {
        $typeInteraction =  TypeInteraction::query()->firstWhere('name', 'created');

        if($typeInteraction == null){
            return response()->errors([], __('Project configuration error'), 500);
        }

        DB::beginTransaction();

        $post = Post::create($request->all());

        if ($request->hasFile('media')) {
            $mediaFiles = $request->file('media');

            $mediaPaths = [];

            foreach ($mediaFiles as $mediaFile) {
                $mediaPath = $mediaFile->store('media/'.auth()->user()->email, 'public');
                $mediaPaths[] = [
                    'url' => Storage::url($mediaPath),
                    'type' => $mediaFile->getClientMimeType(),
                ];
            }

            $post->medias()->createMany($mediaPaths);
        }

        // Récupérer les secteurs à partir de la requête
        $sectors = $request->input('sectors', []);

        // Attacher les secteurs au post nouvellement créé
        $post->sectors()->attach($sectors);

        $post->users()->attach($request->user(), ['type_interaction_id'=> $typeInteraction->id]);

        if(!$post->save()){
            DB::rollBack();
            return response()->errors([], __('Post was\'nt saved error', 400));
        }

        DB::commit();

        return response()->success($post, __('Post created successfully'), 200);
    }

    /**
     * Show the specified post
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        return response()->success($post->loadMissing('postComments', 'creator'), __('Post retrieved successfully'), 200);
    }

    /**
     * Update the specified post
     */
    public function update(PostRequest $request, string $id)
    {
        $typeInteraction =  TypeInteraction::query()->firstWhere('name', 'created');

        if($typeInteraction == null){
            return response()->errors([], __('Project configuration error'), 500);
        }

        $post = Post::with('creator', 'postComments')->find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        if($post->creator->first()->id != $request->user()->id){
            return response()->errors([], __('Unauthorized access to this resource'), 401);
        }

        if(!$post->update($request->all())){
            return response()->errors([], __('Unable to update the resource'), 400);
        }

        // Mettez à jour les médias si de nouveaux fichiers sont fournis
        if ($request->hasFile('media')) {
            $mediaFiles = $request->file('media');

            $mediaPaths = [];

            foreach ($mediaFiles as $mediaFile) {
                $mediaPath = $mediaFile->store('media/'.auth()->user()->email, 'public');
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
     * Delete the specified post
     */
    public function destroy(Request $request, string $id)
    {
        $post = Post::with('creator')->find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        if($post->creator->first()->id != auth()->user()->id){
            return response()->errors([], __('Unauthorized access to this resource'), 401);
        }


        if(!$post->delete()){
            return response()->errors([], __('Unable to update the resource'), 400);
        }

        return response()->success([], __('Post deleted successfully'), 200);
    }


    /**
     * Like the specified post.
     */
    public function like(string $id): JsonResponse
    {
        $post = Post::with('creator')->find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $post->users()->attach(auth()->user(), ['type_interaction_id'=> 2]);


        return response()->success(PostResource::make($post), __('Post liked successfully'), 200);
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

        $post = Post::with('creator')->find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $post->users()->attach(auth()->user(), ['type_interaction_id'=> 3, 'text' => $validated['text']]);

        return response()->success(PostResource::make($post->loadMissing('postComments')), __('Comment added successfully'), 200);
    }

    /**
     * Share the specified post.
     */
    public function share(string $id): JsonResponse
    {
        $post = Post::with('creator')->find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $post->users()->attach(auth()->user(), ['type_interaction_id'=> 4]);

        return response()->success(PostResource::make($post), __('Post shared successfully'), 200);
    }

    /**
     * Delete the specified interaction
     */
    public function deleteInteraction(Request $request, string $id)
    {
        $interaction = Interaction::with('typeInteraction')->find($id);

        if (!$interaction) {
            return response()->errors([], __('Interaction not found'), 404);
        }

        if($interaction->user_id != auth()->user()->id){
            return response()->errors([], __('Unauthorized access to this resource'), 401);
        }

        if($interaction->typeInteraction->id == 1){
            return response()->errors([], __('Unauthorized deletion of this resource'), 401);
        }

        if(!$interaction->delete()){
            return response()->errors([], __('Unable to delete the resource'), 400);
        }

        return response()->success([], __('Interaction deleted successfully'), 200);
    }

}
