<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
        $user = $request->user()->loadMissing('interactions.typeInteraction', 'zone', 'myPosts.medias', 'myPosts.postComments', 'myPosts.zone');

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

        return response()->success(UserResource::make($user), __('User profile retrieved successfully'), 200);
    }

}
