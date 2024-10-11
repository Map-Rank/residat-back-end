<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DisasterResource;
use App\Models\Disaster;

class DisasterController extends Controller
{
    // API: Get all disasters
    public function index()
    {
        $disasters = Disaster::all();
        return response()->success(DisasterResource::collection($disasters), __('Disaster charged successfully'), 200);
    }

    // API: Get a single disaster
    public function show(Disaster $disaster)
    {;
        return response()->success(new DisasterResource($disaster), __('Disaster charged successfully'), 200);
    }
}
