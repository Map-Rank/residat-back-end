<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InteractionResource;
use App\Http\Resources\UserFullResource;
use App\Models\Interaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get user profile information including posts.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('myPosts', 'interactions.typeInteraction', 'zone');

        return response()->success(UserFullResource::make($user), __('User profile retrieved successfully'), 200);
    }

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

}
