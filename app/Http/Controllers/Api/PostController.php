<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use App\Models\User;
use App\Models\Zone;
use App\Models\Media;
use App\Models\Interaction;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Service\UtilService;
use Illuminate\Http\Request;
use App\Models\TypeInteraction;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserFullResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\InteractionResource;

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

        $data = Post::with('creator', 'medias', 'zone');

        // if(Auth::user() != null){
        //     $zone =  Auth::user()->loadMissing('zone.children')->zone;
        //     // Get all the descendants of the user's zone.
        //     if($zone != null){
        //         $descendants = collect();
        //         $descendants->push($zone);
        //         if ($zone->children != null){
        //             $descendants =  UtilService::get_descendants($zone->children, $descendants);
        //         }
        //         $descendantIds = $descendants->pluck('id');
        //         $data = $data->whereIn('zone_id',  $descendantIds);
        //     }
        // }

        if(isset($validated['zone_id'])){
            // $data = $data->where('zone_id', $validated['zone_id']);
            $zone = Zone::find($validated['zone_id']);
            $descendants = collect();
            $descendants->push($zone);
            if ($zone->children != null){
                $descendants =  UtilService::get_descendants($zone->children, $descendants);
            }
            $descendantIds = $descendants->pluck('id');
            $data = $data->whereIn('zone_id',  $descendantIds);
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

        $data =  $data->offSet($page * $size)->take($size)->latest()->where("active", true)->get();

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

        if(strcmp(env('APP_ENV'), 'local') == 0 || strcmp(env('APP_ENV'), 'dev') == 0 || strcmp(env('APP_ENV'), 'testing') == 0){
            if ($request->hasFile('media')) {
                $mediaFiles = $request->file('media');

                $mediaPaths = [];

                foreach ($mediaFiles as $mediaFile) {
                    // $mediaPath = $mediaFile->store('images', 's3');
                    $imageName = Str::uuid() .'.'.$mediaFile->getClientOriginalExtension();
                    $path = Storage::disk('public')->putFileAs('images', $mediaFile, $imageName);
                    $mediaPaths[] = [
                        'url' => $path,
                        'type' => $mediaFile->getClientMimeType(),
                    ];
                }

                $post->medias()->createMany($mediaPaths);
            }
        }else{
            if ($request->hasFile('media')) {
                $mediaFiles = $request->file('media');

                $mediaPaths = [];

                foreach ($mediaFiles as $mediaFile) {
                    // $mediaPath = $mediaFile->store('images', 's3');
                    $imageName =  Str::uuid().'.'.$mediaFile->getClientOriginalExtension();
                    $path = Storage::disk('s3')->putFileAs('images', $mediaFile, $imageName);
                    $mediaPaths[] = [
                        'url' => $path,
                        'type' => $mediaFile->getClientMimeType(),
                    ];
                }

                $post->medias()->createMany($mediaPaths);
            }
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

        $users_token = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

        $users = User::whereNotNull('fcm_token')->get();

        foreach ($users as $user) {
            $customMessage = "Salut {$user->first_name}, regarde ce post sur residat publié le {$post->published_at}. Zone: {$post->zone->name}.";

            try {
                // UtilService::sendWebNotification($post->published_at, $customMessage, $user->fcm_token);
                $notificationService = app(UtilService::class);
                $notificationService->sendNewNotification($post->published_at, $customMessage, [$user->fcm_token]);
            } catch (Exception $ex) {
                Log::warning(sprintf('%s: The error is : %s', __METHOD__, $ex->getMessage()));
            }
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

        return response()->success(PostResource::make($post->loadMissing('medias', 'postComments', 'creator', 'topic', 'shares', 'zone'))
            , __('Post retrieved successfully'), 200);
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
        if(strcmp(env('APP_ENV'), 'local') == 0 || strcmp(env('APP_ENV'), 'dev') == 0 || strcmp(env('APP_ENV'), 'testing') == 0){
            if ($request->hasFile('media')) {
                $mediaFiles = $request->file('media');

                $mediaPaths = [];

                foreach ($mediaFiles as $mediaFile) {
                    // $mediaPath = $mediaFile->store('images', 's3');
                    $imageName = Str::uuid().'.'.$mediaFile->getClientOriginalExtension();
                    $path = Storage::disk('public')->putFileAs('images', $mediaFile, $imageName);
                    $mediaPaths[] = [
                        'url' => Storage::url($path),
                        'type' => $mediaFile->getClientMimeType(),
                    ];
                }

                // Supprimez les anciens médias associés au post
                $post->medias()->delete();

                // Créez les nouveaux médias associés au post
                $post->medias()->createMany($mediaPaths);
            }
        }else{
            if ($request->hasFile('media')) {
                $mediaFiles = $request->file('media');

                $mediaPaths = [];

                foreach ($mediaFiles as $mediaFile) {
                    $imageName = Str::uuid().'.'.$mediaFile->getClientOriginalExtension();
                    $mediaPath = Storage::disk('s3')->putFileAs('images', $mediaFile, $imageName);
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
        }


        return response()->success(PostResource::make($post->loadMissing('medias')), __('Post updated successfully'), 200);
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
     * Like or unlike the specified post.
     */
    public function like(string $id): JsonResponse
    {
        $post = Post::with('creator')->find($id);

        if (!$post) {
            return response()->errors([], __('Post not found'), 404);
        }

        $user = auth()->user();

        // Vérifiez si l'utilisateur a déjà aimé le post
        $isLiked = $post->users()->where('users.id', $user->id)->wherePivot('type_interaction_id',  2)->exists();

        try {
            if ($isLiked) {
                // Si l'utilisateur a déjà aimé le post, retirez le like (unlike) et mettez à jour liked à false
                $post->users()->where('id', $user->id)->wherePivot('type_interaction_id', 2)->detach($user->id);
                $message = __('Post unliked successfully');
            } else {
                // Sinon, ajoutez le like et mettez à jour liked à true
                $post->users()->attach($user, ['type_interaction_id' => 2]);
                $message = __('Post liked successfully');

                // **Créer la notification pour le créateur du post**
                $notificationData = [
                    'user_id' => $post->creator->first()->id, // Créateur du post
                    'titre_en' => "New Like on Your Post",
                    'titre_fr' => "Nouveau j'aime sur votre Post",
                    'content_en' => $user->first_name . " liked your post.",
                    'content_fr' => $user->first_name . " A aimé votre post.",
                    'zone_id' => $post->zone_id,
                ];

                // Envoie la notification au créateur du post
                $creatorToken = $post->creator->first()->fcm_token;
                if ($creatorToken) {
                    // UtilService::sendWebNotification($notificationData['titre_en'], $notificationData['content_en'], [$creatorToken]);
                    $notificationService = app(UtilService::class);
                    $notificationService->sendNewNotification($notificationData['titre_en'], $notificationData['content_en'], [$creatorToken]);
                }
            }

            return response()->success(PostResource::make($post->loadMissing('interactions')), $message, 200);
        } catch (\Exception $e) {
            Log::info(sprintf('%s: User %d generated the error : %s', __METHOD__, auth()->id, $e->getMessage()));
            return response()->errors(['error' => $e->getMessage()], __('Error processing like/unlike'), 500);

            return response()->errors([], $e->getMessage() .__(' Error processing like/unlike'), 500);

        }
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

        $notificationData = [
            'user_id' => $post->creator->first()->id, 
            'titre_en' => "New Comment on Your Post",
            'titre_fr' => "Nouveau Commentaire sur Votre Post",
            'content_en' => auth()->user()->first_name . " commented on your post. ",
            'content_fr' => auth()->user()->first_name . " commented on your post. ",
            'zone_id' => $post->zone_id, 
        ];

        $creatorToken = $post->creator->first()->fcm_token;

        if ($creatorToken) {
            // UtilService::sendWebNotification($notificationData['titre_en'], $notificationData['content_en'], [$creatorToken]);
            $notificationService = app(UtilService::class);
            $notificationService->sendNewNotification($notificationData['titre_en'], $notificationData['content_en'], [$creatorToken]);
        }

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

        $notificationData = [
            'user_id' => $post->creator->first()->id,
            'titre_en' => "Your Post Was Shared",
            'titre_fr' => "Votre Post a été partagé",
            'content_en' => auth()->user()->name . " shared your post.",
            'content_fr' => auth()->user()->name . " a partager votre post.",
            'zone_id' => $post->zone_id, 
        ];

        $creatorToken = $post->creator->first()->fcm_token;
        if ($creatorToken) {
            // UtilService::sendWebNotification($notificationData['titre_en'], $notificationData['content_en'], [$creatorToken]);
            $notificationService = app(UtilService::class);
            $notificationService->sendNewNotification($notificationData['titre_en'], $notificationData['content_en'], [$creatorToken]);
        }

        return response()->success(PostResource::make($post), __('Post shared successfully'), 200);
    }

    /**
     * Delete the specified interaction
     */
    public function deleteInteraction($id)
    {

        $interaction = Interaction::with('typeInteraction')->find($id);

        if (!$interaction) {
            return response()->errors([], __('Interaction not found'), 404);
        }

        if($interaction->user_id != auth()->user()->id){
            return response()->errors([], __('Unauthorized access to this resource'), 401);
        }

        if($interaction->typeInteraction->id == 1 || $interaction->typeInteraction->id == 2){
            return response()->errors([], __('Unauthorized deletion of this resource'), 401);
        }

        if(!$interaction->delete()){
            return response()->errors([], __('Unable to delete the resource'), 400);
        }

        return response()->success([], __('Interaction deleted successfully'), 200);
    }

}
