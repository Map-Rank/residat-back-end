<?php

namespace App\Http\Controllers\Api;


use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        return response()->success(EventResource::collection($events), __('Events retrieved successfully'), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        $event = Event::create($request->validated());
        return response()->success(new EventResource($event), __('Event created successfully'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return response()->success(new EventResource($event), __('Event retrieved successfully'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $event->update($request->validated());
        return response()->success(new EventResource($event), __('Event updated successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->success([], __('Event deleted successfully'), 204);
    }
}
