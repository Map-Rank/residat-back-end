<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserFullResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\InteractionResource;

/**
 * @group Module Profile
 */
class ProfileController extends Controller
{
    /**
     * Get user profile information including posts.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('interactions.typeInteraction', 'zone', 'myPosts.medias', 'myPosts.postComments', 'myPosts.zone','events');

        return response()->success(UserFullResource::make($user), __('User profile retrieved successfully'), 200);
    }

    /**
     * Get another profile information including posts.
     */
    public function showProfile($id): JsonResponse
    {
        $user = User::find($id);
        $user->loadMissing('interactions.typeInteraction', 'zone', 'myPosts.medias', 'myPosts.postComments', 'myPosts.zone');
        if(!$user){
            return response()->errors([], __('User not found !'), 404);
        }

        return response()->success(UserFullResource::make($user), __('User profile retrieved successfully'), 200);
    }

    /**
     * Get interactons posts.
     */
    public function interactions(Request $request) : JsonResponse {
        $validator = Validator::make($request->all(), [
            'type_id'=> ['sometimes', 'integer', 'exists:TypeInteractions,id'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(),  __('bad params'), 400);
        }

        $validated = $validator->validated();


        $interactions = Interaction::query()
            ->where('user_id', $request->user()->id);

        if(isset($validated['type_id'])){
            $interactions = $interactions->where('type_interaction_id', $validated['type_id']);
        }

        return response()->success(InteractionResource::collection($interactions->get()), __('Interactions'), 200);
    }
    
    /**
     * Update profil user
     */
    public function update(ProfileUpdateRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        // Récupérer l'ID du rôle envoyé depuis le formulaire
        $role_id = $request->input('role_id');

        // Vérifier si l'ID du rôle est valide
        if ($role_id) {
            // Récupérer le rôle correspondant à l'ID
            $role = Role::findById($role_id);
            
            // Vérifier si le rôle existe
            if ($role) {
                // Attribuer le rôle à l'utilisateur
                $user->assignRole($role);
            } else {
                // Gérer le cas où le rôle n'existe pas
                return redirect()->back()->with('error', 'Invalid role');
            }
        }

        if(strcmp(env('APP_ENV'), 'local') == 0 || strcmp(env('APP_ENV'), 'dev') == 0 || strcmp(env('APP_ENV'), 'testing') == 0){
            // Vérifiez si un fichier d'avatar a été téléchargé
            if ($request->hasFile('avatar')) {
                // Récupérez le fichier d'avatar téléchargé
                $avatar = $request->file('avatar');
                
                // Générez un nom de fichier unique pour l'avatar
                $avatarName = uniqid('avatar_') . '.' . $avatar->getClientOriginalExtension();

                // Stockez l'avatar dans le dossier de stockage
                $avatarPath = $avatar->storeAs('media/avatar/'.$user->email, $avatarName, 'public');

                // Mettez à jour l'attribut d'avatar de l'utilisateur avec le chemin d'accès au fichier
                $user->avatar = $avatarPath;


                $user->load('zone');

                // Sauvegardez les modifications apportées à l'utilisateur
                $user->save();
            }
        }else{
            // Vérifiez si un fichier d'avatar a été téléchargé
            if ($request->hasFile('avatar')) {
                // Récupérez le fichier d'avatar téléchargé
                $avatar = $request->file('avatar');
                
                // Générez un nom de fichier unique pour l'avatar
                $avatarName = uniqid('avatar_') . '.' . $avatar->getClientOriginalExtension();

                // Stockez l'avatar dans le dossier de stockage
                $avatarPath = $avatar->storeAs('media/avatar/'.$user->email, $avatarName, 's3');

                // Mettez à jour l'attribut d'avatar de l'utilisateur avec le chemin d'accès au fichier
                $user->avatar = $avatarPath;


                $user->load('zone');

                // Sauvegardez les modifications apportées à l'utilisateur
                $user->save();
            }
        }

        return response()->success(UserResource::make($user), __('User profile retrieved successfully'), 200);
    }
    
    /**
     * Delete own account
     */
    public function destroy()
    {
        // Démarrer la transaction
        DB::beginTransaction();

        try {
            // Trouver l'utilisateur par son ID*
            $id =  Auth::id();
            $user = User::findOrFail($id);

            // Supprimer les interactions de l'utilisateur
            $user->interactions()->delete();
            $user->likeInteractions()->delete();
            $user->commentInteractions()->delete();
            $user->shareInteractions()->delete();

            // Supprimer les abonnements de l'utilisateur
            // $user->subscriptions()->detach();
            // $user->activeSubscription()->detach();

            // Supprimer les notifications de l'utilisateur
            $user->notifications()->delete();

            // Supprimer les feedbacks de l'utilisateur
            $user->feedbacks()->delete();

            // Supprimer les événements de l'utilisateur
            $user->events()->delete();

            // Supprimer les suivis de l'utilisateur
            $user->followers()->detach();
            $user->following()->detach();

            // Supprimer l'avatar de l'utilisateur s'il existe
            if ($user->avatar) {
                $disk = env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3';
                Storage::disk($disk)->delete($user->avatar);
            }
            // dd($user);

            // Supprimer l'utilisateur
            $user->delete();


            // Valider la transaction
            DB::commit();

            return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès');
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            Log::error('Erreur lors de la suppression de l\'utilisateur' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Erreur lors de la suppression de l\'utilisateur');
        }
    }

}
