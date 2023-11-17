<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\ResetPasswordWithOtp;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;

/**
 * Class PasswordController
 * @package App\Http\Controllers\Api
 * @group Module Authentification
 */
class PasswordController extends Controller
{

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     * @unauthenticated
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        
        $status = Password::sendResetLink(
            $request->only('email')
        );
        
        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->success([], __($status) , 200);
    }


    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @unauthenticated
     */
    public function reset(ResetPasswordRequest $request)
    {
        $request->only('email', 'password');

        $user = User::where('email',$request->email);
        $user->update([
            'password'=>Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        event(new PasswordReset($user));

        return response()->success([], __("Your password has been reset"), 200);
    }
}

