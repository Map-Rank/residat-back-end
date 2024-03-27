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
     * List all events
     */
    public function index()
    {
        $events = Event::all();
        return response()->success(EventResource::collection($events), __('Events retrieved successfully'), 200);
    }

    /**
     * Store event.
     */
    public function store(EventRequest $request)
    {
        $validatedData = $request->validated();
        $event = Event::create($validatedData);

        if ($request->hasFile('media')) {
            $file = $request->file('media');

            $fileName = uniqid('media_'. $event->id) . '.' . $file->getClientOriginalExtension();

            $filePath = $file->storeAs('storage/media/events/', $fileName);

            $event->file = $filePath;

            $event->save();
        }

        
        return response()->success(new EventResource($event), __('Event created successfully'), 201);
    }

    /**
     * Show event
     */
    public function show(Event $event)
    {
        return response()->success(new EventResource($event), __('Event retrieved successfully'), 200);
    }

    /**
     * Update event.
     */
    public function update(EventRequest $request, Event $event)
    {
        $validatedData = $request->validated();
        $event->update($validatedData);

        if ($request->hasFile('media')) {
            $file = $request->file('media');

            $fileName = uniqid('media_'. $event->id) . '.' . $file->getClientOriginalExtension();

            $filePath = $file->storeAs('storage/media/events/', $fileName);

            $event->file = $filePath;

            $event->save();
        }

        
        return response()->success(new EventResource($event), __('Event updated successfully'), 200);
    }

    /**
     * Delete event.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->success([], __('Event deleted successfully'), 204);
    }
}
