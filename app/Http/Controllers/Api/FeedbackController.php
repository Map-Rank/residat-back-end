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

        if ($request->hasFile('file')) {
            $mediaFile = $request->file('file');
            $imageName = time().'.'.$mediaFile->getClientOriginalExtension();
            $feedback['file'] = Storage::disk('s3')->putFileAs('feedbacks', $mediaFile, $imageName);
            $feedback->save();
        }

        return response()->success(new FeedbackResource($feedback), __('Feedback created successfully'), 201);
    }
}
