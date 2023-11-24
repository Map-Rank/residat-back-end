<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/**
 * @group Module Authentification
 */
class EmailVerificationController extends Controller
{
    /**
     * Verify email
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->success([
                "verified" => false,
                "link_verification" => false,
                "already_verified" => true],
                 __('Email already verified'));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->success([
            "verified" => true,
            "link_verification" => false,
            "already_verified" => false
        ], __('Email has been verified'));
    }


    /**
     * Resend email verification
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->success([
                "verified" => false,
                "link_verification" => false,
                "already_verified" => true
            ], __('Email already verified'));
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->success([
            "verified" => false,
            "link_verification" => true,
            "already_verified" => false
        ], __('New email verification link sent successfully.'));
    }
}
