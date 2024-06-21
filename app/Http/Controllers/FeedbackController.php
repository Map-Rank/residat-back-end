<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\FeedbackRequest;
use App\Http\Resources\FeedbackResource;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feedbacks = Feedback::paginate(10);
        
        return view('feedbacks.index',[
            'feedbacks' => $feedbacks,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        $feedback->load('user');

        return view('feedback.show',[
            'feedbacks' => $feedback,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        // Vérifier si l'utilisateur authentifié est autorisé à supprimer le feedback
        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            return response()->errors([], __('Unauthorized'), 401);
        }
        
        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted successfully']);
    }
}
