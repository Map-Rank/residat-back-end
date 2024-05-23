<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Report;
use App\Models\Vector;
use App\Models\VectorKey;
use App\Models\MetricType;
use App\Models\ReportItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function  index(Request $request){
        $reports = Report::query()->paginate(50);
        return view('reports.index', compact('reports'));
    }

    public function create(){
        $types = ["DROUGHT", "FLOOD", "WATER_STRESS"];
        $metricTypes = MetricType::all();

        $zones = Zone::query()->where('level_id', 4)->get();

        return view('reports.create', compact('types', 'zones', 'metricTypes'));
    }

    public function createHealth(){

        $zones = Zone::query()->where('level_id', 4)->get();

        return view('reports.health-create', compact('zones'));
    }

    public function createAgriculture(){

        $zones = Zone::query()->where('level_id', 4)->get();

        return view('reports.agriculture-create', compact('zones'));
    }

    public function store(ReportRequest $request){

        // dd($request->all());

        $validatedData = $request->validated();

        $user = Auth::user();

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
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('report_images/', 'public');
            $report->image = $imagePath;
            $report->save();
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

        if(isset($validatedData['report_items'])){
            // Création des éléments de rapport
            foreach ($validatedData['report_items'] as $itemData) {
                $reportItem = ReportItem::create([
                    'report_id' => $report->id,
                    'metric_type_id' => $itemData['metric_type_id'],
                    'value' => $itemData['value'],
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
