<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::paginate(10);
        
        return view('events.index',[
            'events' => $events,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Vérifier si l'utilisateur authentifié est autorisé à supprimer le feedback
        $user = Auth::user();

        if (!$user->hasRole('admin')) {
            return response()->errors([], __('Unauthorized'), 401);
        }
        
        $event->delete();

        return redirect()->back()->with('success', 'Events deleted successfully');
    }
}
