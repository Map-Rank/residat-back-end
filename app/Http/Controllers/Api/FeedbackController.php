<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
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

        return response()->success(new FeedbackResource($feedback), __('Feedback created successfully'), 201);
    }
}
