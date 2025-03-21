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
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

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
                'phone' => $data['phone'],
                'active' => 1,
                'activated_at' => Carbon::now(),
                'password' => Hash::make($data['password']),
                'profession' => 'INSTITUTION',
                'description' => $data['description'],
                'email_verified_at' => false,
                'language' => $data['language'],
                'first_name' => $data['company_name'],
                'avatar' => isset($data['profile']) ? $data['profile'] : '',
                'fcm_token' => isset($data['fcm_token']) ? $data['fcm_token'] : '',
                'type' => 'COUNCIL',
                'zone_id' => $data['zone_id']
            ];
            $user = User::create($userData);
            // Attribuer le rôle par défaut (par exemple, 'default') à l'utilisateur
            $defaultRole = Role::where('name', 'default')->first();

            if ($defaultRole) {
                $user->assignRole($defaultRole);
            }
            $token = $user->createToken('authtoken');
            $userData = UserResource::make($user->loadMissing('zone'))->toArray($request);
            $userData['token'] = $token->plainTextToken;

            Mail::to($company->email)->send(new CompanyCreated($company));
        }

        if (!$userData['email_verified_at']) {
            $userData['token'] = $token->plainTextToken;
            $userData['verified'] = false;
            $userData['type'] = 'COUNCIL';
            $userData['company'] = new CompanyResource($company);

            return response()->success($userData, __('Please verify you mail') , 200);
        }

        // return new CompanyResource($company);
        return response()->success(['company' => new CompanyResource($company),
            'user' => $userData], __('Company created successfully'), 200);
    }

}
