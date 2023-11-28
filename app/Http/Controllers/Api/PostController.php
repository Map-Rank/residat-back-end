<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Service\UtilService;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
