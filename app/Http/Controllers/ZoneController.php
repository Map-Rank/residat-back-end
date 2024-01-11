<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZoneRequest;
use App\Http\Resources\ZoneResource;
use App\Models\Zone;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function index(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string'],
            'parent_id'=> ['sometimes', 'int'],
            'level_id'=> ['sometimes', 'int'],
        ]);

        if ($validator->fails()) {
            return response()->error($validator->failed(), __('Bad parameters'), 400);
        }

        $validated = $validator->validated();
        $data = Zone::with('children');
        if(isset($validated['name'])){
            $data = $data->where('name', 'like' , '%'.$validated['name'].'%');
        }

        if(isset($validated['parent_id'])){
            $data = $data->where('parent_id' , $validated['parent_id']);
        }
        
        $zones = $data->where('level_id' , 2)->get();

        return view('zones.regions', compact('zones'));
        // return response()->success(ZoneResource::collection($data), __('Values found'));
    }

    public function divisions(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'parent_id'=> ['sometimes', 'int'],
        ]);

        if ($validator->fails()) {
            return response()->error($validator->failed(), __('Bad parameters'), 400);
        }

        $validated = $validator->validated();
        $data = Zone::with('children');
        if(isset($validated['name'])){
            $data = $data->where('name', 'like' , '%'.$validated['name'].'%');
        }

        if(isset($validated['parent_id'])){
            $data = $data->where('parent_id' , $validated['parent_id']);
        }
        
        $divisions = $data->where('parent_id' , $id)->get();

        return view('zones.divisions', compact('divisions'));
    }

    public function subdivisions(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'parent_id'=> ['sometimes', 'int'],
        ]);

        if ($validator->fails()) {
            return response()->error($validator->failed(), __('Bad parameters'), 400);
        }

        $validated = $validator->validated();
        $data = Zone::with('children');
        if(isset($validated['name'])){
            $data = $data->where('name', 'like' , '%'.$validated['name'].'%');
        }

        if(isset($validated['parent_id'])){
            $data = $data->where('parent_id' , $validated['parent_id']);
        }
        
        $subdivisions = $data->where('parent_id' , $id)->get();

        return view('zones.subdivisions', compact('subdivisions'));
    }

    public function show($id) {

        $data = Zone::query()->find($id);
        $data->loadMissing(['parent']);

        if (!$data) {
            return redirect()->back()->with('error', 'Zone not found');
        }

        return view('zones.show', compact('data'));
    }

    /**
     * Create and store a zone
     *
     * @param ZoneRequest $request List of elements used to save a zone entity.
     * @return JsonResponse
     */
    public function store(ZoneRequest $request) : JsonResponse {

        $datum = new Zone($request->validated());

        return (!$datum->save())
            ? response()->notFoundId()
            : response()->created(ZoneResource::make($datum), __('Zone successfully created!'), 201);
    }

    /**
     * Update the specified zone
     *
     * @param ZoneRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ZoneRequest $request, int $id): JsonResponse
    {
        $zone = Zone::query()->find($id);
        if(!$zone)
        { return response()->notFoundId(); }

        return (! $zone->update($request->validated()))
            ? response()->notFoundId()
            : response()->success(ZoneResource::make($zone), __('Zone successfully updated!'), 200);
    }

    /**
     * Delete the specified zone.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $zone = Zone::query()->find($id);
        
        if (!$zone) {
            return redirect()->back()->with('error', 'Zone not found');
        }

        return redirect()->back()->with('sucess', 'Zone successfully deleted!');
    }
}
