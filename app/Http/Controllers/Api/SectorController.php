<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectorFullResource;
use App\Http\Resources\SectorResource;
use App\Models\Sector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectorController extends Controller
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
        $data = Sector::query();
        if(isset($validated['name'])){
            $data = $data->where('name', 'like' , '%'.$validated['name'].'%');
        }

        $data = $data->get();

        return response()->success(SectorResource::collection($data), __('Values found'));
    }


    /**
     * Display the resource.
     *
     * @param int $id Id of the resource entity
     * @return JsonResponse
     */
    public function show($id) : JsonResponse {

        $datum = Sector::query()->find($id);
        return (!$datum)
            ? response()->errors([], __('Zone not found'), 404)
            : response()->success(SectorFullResource::make($datum->loadMissing(['posts']),__('Values found')));
    }
}
