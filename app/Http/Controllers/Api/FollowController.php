<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @group Module Follow
 */
class FollowController extends Controller
{
    /**
     * Follow user
     */
    public function follow(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $authUser = $request->user();

        // Check if already following
        $alreadyFollowing = $authUser->following()->where('followed_id', $id)->exists();

        if ($alreadyFollowing) {
            return response()->success([], __('You already follow this user.'), 200);
        }

        // Follow the user
        $authUser->following()->attach($user->id);

        return response()->success([], __('Successfully followed user.'), 200);
    }

    /**
     * Unfollow user
     */
    public function unfollow(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $authUser = $request->user();

        // Check if not following
        $alreadyFollowing = $authUser->following()->where('followed_id', $id)->exists();

        if (!$alreadyFollowing) {
            return response()->success([], __('You don\'t follow this user.'), 200);
        }

        // Unfollow the user
        $authUser->following()->detach($user->id);

        return response()->success([], __('You Successfully unfollowed user.'), 200);
    }

    /**
     * Follower of specific user
     */
    public function followers($id)
    {
        $user = User::with('followers')->findOrFail($id);
        return response()->success($user->followers, __('Successfully unfollowed user.'), 200);
    }

    /**
     * Following of specific user
     */
    public function following($id)
    {
        $user = User::with('following')->findOrFail($id);
        return response()->success($user->following, __('Successfully unfollowed user.'), 200);
    }
}
