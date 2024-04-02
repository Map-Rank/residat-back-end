<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Zone;
use App\Models\Event;
use App\Service\UtilService;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * List all events
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['sometimes','numeric'],
            'size'=> ['sometimes', 'numeric'],
            'zone_id'=> ['sometimes', 'integer', 'exists:zones,id'],
            'sector'=> ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(),  __('bad params'), 400);
        }

        $validated = $validator->validated();

        $page = $validated['page'] ?? 0;
        $size = $validated['size'] ?? 10;

        $data = Event::with('zone');

        if(isset($validated['zone_id'])){
            $zone = Zone::find($validated['zone_id']);
            $descendants = collect();
            $descendants->push($zone);
            if ($zone->children != null){
                $descendants =  UtilService::get_descendants($zone->children, $descendants);
            }
            $descendantIds = $descendants->pluck('id');
            $data = $data->whereIn('zone_id',  $descendantIds);
        }

        if(isset($validated['sector'])){
            try{
                $sectorIds = json_decode($validated['sector'], JSON_THROW_ON_ERROR);
                if(is_array($sectorIds)){
                    $data = $data->whereIn('sector_id', $sectorIds);
                }
            }catch(Exception $ex){
                Log::warning(sprintf('%s: The error is : %s', __METHOD__, $ex->getMessage()));
            }
        }

        $events =  $data->offSet($page * $size)->take($size)->latest()->get();

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

            $event->media = $filePath;

            $event->save();
        }


        return response()->success(new EventResource($event), __('Event created successfully'), 201);
    }

    /**
     * Show event
     */
    public function show(Event $event)
    {
        $event->load('user');
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

            $event->media = $filePath;

            $event->save();
        }


        return response()->success(new EventResource($event), __('Event updated successfully'), 200);
    }

    /**
     * Delete event.
     */
    public function destroy($id)
    {
        $datum = Event::query()->find($id);

        if (!$datum) {
            return response()->errors([], __('Event not found'), 404);
        }

        if($datum->user_id != auth()->user()->id){
            return response()->errors([], __('Unauthorized deletion to this resource'), 401);
        }


        if(!$datum->delete()){
            return response()->errors([], __('Unable to update the resource'), 400);
        }

        return response()->success([], __('Event deleted successfully'));
    }
}
