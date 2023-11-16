<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
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
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $token = $request->user()->createToken('authtokensag');

        if (!$this->InvalidCredential($request)) {
            return response()->success([], __('Invalid login credentials') , 200);
        }

        if (!$this->emailIsVerified()) {
            return response()->success(['token' => $token->plainTextToken, "verified" => false], __('Please verify you mail') , 200);
        }

        if (!$this->accountIsActived()) {
            return response()->success(['token' => $token->plainTextToken, "isActive" => false], __('Please wait for activation') , 200);
        }

        return response()->json(
            [
                'status' => true,
                'message' => __('You are Logged'),
                'user' => new UserResource(Auth::user()),
                'token' => $token->plainTextToken
            ],200
        );
    }
    /**
     * Attempting to log in with the credentials provided.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function InvalidCredential($request)
    {
        return Auth::attempt($request->only('email', 'password'));
    }

    /**
     * Checks if the user's email has been verified.
     *
     * @return mixed
     */
    private function emailIsVerified()
    {
        return Auth::user()->email_verified_at;
    }

    /**
     * Checks if the user's account is activated.
     *
     * @return mixed
     */
    private function accountIsActived()
    {
        return Auth::user()->active;
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
