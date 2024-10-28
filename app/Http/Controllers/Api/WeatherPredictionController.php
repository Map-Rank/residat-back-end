<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Zone;
use App\Models\Level;
use App\Models\Vector;
use App\Models\VectorKey;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ZoneRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ZoneResource;
use App\Models\WeatherPrediction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WeatherPredictionController extends Controller
{

}
