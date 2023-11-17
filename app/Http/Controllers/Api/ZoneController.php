<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->error($validator->failed(), __('Bad parameters'), 400);
        }

        $validated = $validator->validated();
        $data = Zone::query();
        if(isset($validated['name'])){
            $data = $data->where('name', 'like' , '%'.$validated['name'].'%');
        }

        $data = $data->get();

        return response()->success(ZoneResource::collection($data), __('Values found'));
    }

    /**
     * Display the resource.
     *
     * @param int $id Id of the resource entity
     * @return JsonResponse
     */
    public function show($id) : JsonResponse {

        $datum = Zone::query()->find($id);
        return (!$datum)
            ? response()->error([], __('Zone not found'), 404)
            : response()->success(ZoneResource::make($datum->loadMissing(['parent']),__('Values found')));
    }

    /**
     * Create and store a zone entity.
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
     * Update the specified resource entity.
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
     * Remove the specified resource entity.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $zone = Zone::query()->find($id);
        if(!$zone)
        { return response()->notFoundId(); }

        return (!$zone->delete())
            ? response()->error($zone,__('Zone not deleted!'), 400)
            : response()->success(ZoneResource::make($zone), __('Zone successfully deleted!'), 200);
    }
}