<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserCreate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 * @group Module Authentification
 */
class AuthController extends Controller
{

    /**
     * Manages user registration.
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     * @unauthenticated
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->all());

        $token = $user->createToken('authtoken');

        $userData = UserResource::make($user)->toArray($request);
        $userData['token'] = $token->plainTextToken;

        return response()->success($userData, __('User registered. Please check your email'), 201);
    }

    /**
     * Handles the user's login attempt.
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $token = $request->user()->createToken('authtoken');

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->success([], __('Invalid login credentials') , 200);
        }

        if (!Auth::user()->email_verified_at) {
            return response()->success(['token' => $token->plainTextToken, "verified" => false], __('Please verify you mail') , 200);
        }

        if (!Auth::user()->active) {
            return response()->success(['token' => $token->plainTextToken, "isActive" => false], __('Please wait for activation') , 200);
        }

        $user = UserResource::make(Auth::user())->toArray($request);
        $user['token'] = $token->plainTextToken;

        return response()->success($user, __('You are logged in'), 200);
    }

    /**
     * Logs out the user
     * 
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->success([], __('You Logged out') , 200);
    }

    /**
     * Checks the validity of the Sanctum authentication token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function verifyToken(Request $request)
    {
        // Vérifiez si l'utilisateur est authentifié via Sanctum
        if (Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Token is valid'], 200);
        } else {
            return response()->json(['message' => 'Token is not valid'], 401);
        }
    }

}