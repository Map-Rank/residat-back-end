<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\FeedbackResource;

/**
 * @group Module Feedback
 */
class FeedbackController extends Controller
{
    /**
     * Store a new feedback.
     */
    public function store(FeedbackRequest $request)
    {
        $validatedData = $request->validated();

        // Get the currently logged-in user's ID
        $userId = auth()->user()->id;

        // Add user ID to the validated data
        $validatedData['user_id'] = $userId;
        
        $feedback = Feedback::create($validatedData);

        if(strcmp(env('APP_ENV'), 'local') == 0 || strcmp(env('APP_ENV'), 'dev') == 0 || strcmp(env('APP_ENV'), 'testing') == 0){
            if ($request->hasFile('file')) {
                $mediaFile = $request->file('file');
                $imageName = time().'.'.$mediaFile->getClientOriginalExtension();
                $feedback['file'] = Storage::disk('public')->putFileAs('feedbacks', $mediaFile, $imageName);
                $feedback->save();
            }
        }else{
            if ($request->hasFile('file')) {
                $mediaFile = $request->file('file');
                $imageName = time().'.'.$mediaFile->getClientOriginalExtension();
                $feedback['file'] = Storage::disk('s3')->putFileAs('feedbacks', $mediaFile, $imageName);
                $feedback->save();
            }
        }


        return response()->success(new FeedbackResource($feedback), __('Feedback created successfully'), 201);
    }
}
