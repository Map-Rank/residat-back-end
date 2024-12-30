<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Report;
use App\Models\Vector;
use App\Models\VectorKey;
use App\Models\MetricType;
use App\Models\ReportItem;
use Illuminate\Support\Str;
use App\Service\UtilService;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use App\Models\SubMetricType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index(Request $request){
        $reports = Report::query()->paginate(50);
        return view('reports.index', compact('reports'));
    }

    public function createResourceCompletion(){
        // Récupérer l'utilisateur connecté
        $user = auth()->user();
        
        // Obtenir les zones avec level_id égal à 4
        $zones = UtilService::getZonesWithLevelId4ForUser($user);

        return view('reports.ressource-completion', compact('zones'));
    }

    public function createFishingVulnerability(){
        $zones = Zone::query()->where('level_id', 4)->get();

        return view('reports.fishing-vulnerability', compact('zones'));
    }

    public function createWaterStress(){
        // Récupérer l'utilisateur connecté
        $user = auth()->user();
        
        // Obtenir les zones avec level_id égal à 4
        $zones = UtilService::getZonesWithLevelId4ForUser($user);

        return view('reports.water-stress', compact('zones'));
    }

    public function createMigration(){
        
        // Récupérer l'utilisateur connecté
        $user = auth()->user();
        
        // Obtenir les zones avec level_id égal à 4
        $zones = UtilService::getZonesWithLevelId4ForUser($user);

        // Passer les zones à la vue
        return view('reports.migration', compact('zones'));
    }

    public function createHealth(){

        // Récupérer l'utilisateur connecté
        $user = auth()->user();
        
        // Obtenir les zones avec level_id égal à 4
        $zones = UtilService::getZonesWithLevelId4ForUser($user);

        return view('reports.health-create', compact('zones'));
    }

    public function createAgriculture(){

        // Récupérer l'utilisateur connecté
        $user = auth()->user();
        
        // Obtenir les zones avec level_id égal à 4
        $zones = UtilService::getZonesWithLevelId4ForUser($user);

        return view('reports.agriculture-create', compact('zones'));
    }

    public function createInfrastructure(){

        // Récupérer l'utilisateur connecté
        $user = auth()->user();
        
        // Obtenir les zones avec level_id égal à 4
        $zones = UtilService::getZonesWithLevelId4ForUser($user);

        return view('reports.infrastructure-create', compact('zones'));
    }

    public function createSocial(){

        // Récupérer l'utilisateur connecté
        $user = auth()->user();
        
        // Obtenir les zones avec level_id égal à 4
        $zones = UtilService::getZonesWithLevelId4ForUser($user);

        return view('reports.social-create', compact('zones'));
    }

    public function createSelectSecurity(){

        // Récupérer l'utilisateur connecté
        $user = auth()->user();
        
        // Obtenir les zones avec level_id égal à 4
        $zones = UtilService::getZonesWithLevelId4ForUser($user);

        return view('reports.food-security', compact('zones'));
    }
    
    

    public function store(ReportRequest $request){

        // dd($request->all());

        $validatedData = $request->validated();

        $user = Auth::user();

        $metricTypes = MetricType::all();

        $report = Report::create([
            'code' => Str::uuid(),
            'user_id' => $user->id,
            'zone_id' => $validatedData['zone_id'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        // Enregistrer l'image si elle est fournie
        if(strcmp(env('APP_ENV'), 'local') == 0 || strcmp(env('APP_ENV'), 'dev') == 0 || strcmp(env('APP_ENV'), 'testing') == 0){
            if ($request->hasFile('image')) {

                $mediaFile = $request->file('image');
                $imageName = time().'.'.$mediaFile->getClientOriginalExtension();
                $imagePath = Storage::disk('public')->putFileAs('images', $mediaFile, $imageName);
                $report->image = $imagePath;
                $report->save();
            }

        }else{
            if ($request->hasFile('image')) {
                $mediaFile = $request->file('image');
                $imageName = time().'.'.$mediaFile->getClientOriginalExtension();
                $imagePath = Storage::disk('s3')->putFileAs('images', $mediaFile, $imageName);
                $report->image = $imagePath;
                $report->save();
            }
        }

        $vector = Vector::create([
            'path' => $imagePath,
            'model_id' => $report->id,
            'category' => $validatedData['type'],
            'type' => 'SVG',
            'model_type' => Report::class,
        ]);

        // Création des clés de vecteur pour le vecteur
        if(isset($validatedData['vector_keys'])){
            foreach ($validatedData['vector_keys'] as $keyData) {
                $vectorKey = VectorKey::create([
                    'value' => $keyData['value'],
                    'type' => $keyData['type'],
                    'name' => $keyData['name'],
                    'vector_id' => $vector->id,
                ]);
            }
        }

        return redirect()->route('reports.index')->with('success', 'Report created successfully');
    }

    /**
     * Delete the specified zone.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $datum = Report::query()->find($id);

        if (!$datum) {
            return redirect()->back()->with('errors', 'Report not found');
        }

        $datum->delete();

        return redirect()->back()->with('success', 'Report successfully deleted!');
    }
}
