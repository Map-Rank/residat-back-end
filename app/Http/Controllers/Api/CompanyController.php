<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Mail\CompanyCreated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
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
            Mail::to($company->email)->send(new CompanyCreated($company));
        }

        // return new CompanyResource($company);
        return response()->success(new CompanyResource($company), __('Company created successfully'), 201);
    }

}
