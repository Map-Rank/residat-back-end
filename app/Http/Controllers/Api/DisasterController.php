<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DisasterResource;
use App\Models\Disaster;

/**
 * @group Module Disaster
 */
class DisasterController extends Controller
{
    /**
     * List all disasters
     */
    public function index()
    {
        $disasters = Disaster::all();
        return response()->success(DisasterResource::collection($disasters), __('Disaster charged successfully'), 200);
    }

    /**
     * Show disaster
     */
    public function show(Disaster $disaster)
    {;
        return response()->success(new DisasterResource($disaster), __('Disaster charged successfully'), 200);
    }
}
