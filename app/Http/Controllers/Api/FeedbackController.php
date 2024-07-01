<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\FeedbackResource;

class FeedbackController extends Controller
{
    /**
     * Store a new feedback.
     */
    public function store(FeedbackRequest $request)
    {
        $validatedData = $request->validated();
        
        $feedback = Feedback::create($validatedData);

        if ($request->hasFile('file')) {
            $mediaFile = $request->file('file');
            $mediaPath = $mediaFile->store('media/feedbacks/'.auth()->user()->email, 's3');
            $feedback['file'] = Storage::url($mediaPath);
            $feedback->save();
        }

        return response()->success(new FeedbackResource($feedback), __('Feedback created successfully'), 201);
    }
}
