<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @group Module Permissions
 */
class FollowController extends Controller
{
    /**
     * Follow user
     */
    public function follow(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if($user->following->where('id', $id)->count() > 0){
            return response()->success([], __('You already follow this user.'), 200);
        }

        $request->user()->following()->attach($user->id);

        return response()->success([], __('Successfully followed user.'), 200);
    }

    /**
     * Unfollow user
     */
    public function unfollow(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if($user->following->where('id', $id)->count() == 0){
            return response()->success([], __('You don\'t follow this user.'), 200);
        }

        $request->user()->following()->detach($user->id);

        return response()->success([], __('Successfully unfollowed user.'), 200);
    }

    /**
     * Follow user
     */
    public function followers($id)
    {
        $user = User::with('followers')->findOrFail($id);
        return response()->success($user->followers, __('Successfully unfollowed user.'), 200);
    }

    public function following($id)
    {
        $user = User::with('following')->findOrFail($id);
        return response()->success($user->following, __('Successfully unfollowed user.'), 200);
    }
}
