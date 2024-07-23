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

        if(env('APP_ENV') == "local" || env('APP_ENV')  == "dev" || env('APP_ENV') == "testing"){

            if ($request->hasFile('profile')) {
                $mediaFileProfile = $request->file('profile');
                $imageNameProfile = time().'.'.$mediaFileProfile->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('company_profile_pictures', $mediaFileProfile, $imageNameProfile);
                $data['profile'] = $path;
            }
    
            if ($request->hasFile('official_document')) {
                $mediaFileDoc = $request->file('official_document');
                $imageNameDoc = time().'.'.$mediaFileDoc->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('company_official_document', $mediaFileDoc, $imageNameDoc);
                $data['official_document'] = $path;
            }
        }else{

            if ($request->hasFile('profile')) {
                $mediaFileProfile = $request->file('profile');
                $imageNameProfile = time().'.'.$mediaFileProfile->getClientOriginalExtension();
                $path = Storage::disk('s3')->putFileAs('company_profile_pictures', $mediaFileProfile, $imageNameProfile);
                $data['profile'] = $path;
            }
    
            if ($request->hasFile('official_document')) {
                $mediaFileDoc = $request->file('official_document');
                $imageNameDoc = time().'.'.$mediaFileDoc->getClientOriginalExtension();
                $path = Storage::disk('s3')->putFileAs('company_official_document', $mediaFileDoc, $imageNameDoc);
                $data['official_document'] = $path;
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
