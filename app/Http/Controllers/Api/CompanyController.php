<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Mail\CompanyCreated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * @group Module Requests
 */
class CompanyController extends Controller
{

    /**
     * Create Request
     */
    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        $path = '';

        if(strcmp(env('APP_ENV'), 'local') == 0 || strcmp(env('APP_ENV'), 'dev') == 0 || strcmp(env('APP_ENV'), 'testing') == 0){

            if ($request->hasFile('profile')) {
                $mediaFileProfile = $request->file('profile');
                $imageNameProfile = time().'.'.$mediaFileProfile->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('company_profile_pictures', $mediaFileProfile, $imageNameProfile);
                $data['profile'] = $path;
            }
        }else{

            if ($request->hasFile('profile')) {
                $mediaFileProfile = $request->file('profile');
                $imageNameProfile = time().'.'.$mediaFileProfile->getClientOriginalExtension();
                $path = Storage::disk('s3')->putFileAs('company_profile_pictures', $mediaFileProfile, $imageNameProfile);
                $data['profile'] = $path;
            }
        }



        $company = Company::create($data);

        if($company){
            // Envoyer l'email de notification
            $userData = [
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'profession' => $data['email'],
                'description' => $data['description'],
                'language' => $data['language'],
                'first_name' => $data['company_name'],
                'avatar' => isset($data['profile']) ? $data['profile'] : '',
                'fcm_token' => isset($data['fcm_token']) ? $data['fcm_token'] : '',
                'type' => 'COUNCIL',
            ];
            $user = User::create($userData);
            $token = $user->createToken('authtoken');
            $userData = UserResource::make($user->loadMissing('zone'));
            $userData['token'] = $token->plainTextToken;

            Mail::to($company->email)->send(new CompanyCreated($company));
        }

        // return new CompanyResource($company);
        return response()->success(['company' => new CompanyResource($company),
            'user' => $userData], __('Company created successfully'), 201);
    }

}
