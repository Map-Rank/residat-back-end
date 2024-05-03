<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Zone;
use App\Models\Report;
use App\Models\Vector;
use App\Models\VectorKey;
use App\Models\MetricType;
use App\Models\ReportItem;
use Illuminate\Support\Str;
use App\Service\UtilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function  index(Request $request){

        $validator = Validator::make($request->all(), [
            'page' => ['sometimes','numeric'],
            'size'=> ['sometimes', 'numeric'],
            'type'=> ['sometimes', 'numeric'],
            'start_date'=> ['sometimes', 'date'],
            'end_date'=> ['sometimes', 'date'],
            'zone_id'=> ['sometimes', 'integer', 'exists:zones,id'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(),  __('bad params'), 400);
        }

        $validated = $validator->validated();

        $page = $validated['page'] ?? 0;
        $size = $validated['size'] ?? 10;

        $data = Report::with('zone', 'items', 'vector.keys');

        if (isset($validated['zone_id'])) {
            $data->where('zone_id', $validated['zone_id']);
        }

        // Filtrer par date de début si une date de début est spécifiée dans la demande
        if (isset($validated['start_date'])) {
            $data->whereDate('start_date', $validated['start_date']);
        }

        // Filtrer par date de fin si une date de fin est spécifiée dans la demande
        if (isset($validated['end_date'])) {
            $data->whereDate('end_date', $validated['end_date']);
        }

        // Filtrer par type si un type est spécifié dans la demande
        if ($request->has('type')) {
            $data->where('type', $validated['type']);
        }

        $reports =  $data->offSet($page * $size)->take($size)->latest()->get();

        return response()->success($reports, __('Reports charged successfully'), 200);
    }

    public function store(ReportRequest $request){

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
            $imagePath = $request->file('image')->store('report_images');
            $report->image = $imagePath;
            $report->save();
        }

        $vector = Vector::create([
            'path' => $imagePath,
            'model_id' => $report->id,
            'category' => $validatedData['type'],
            'type' => $request->file('image')->getClientMimeType(),
            'model_type' => 'App\\Models\\Report',
        ]);

        // Création des clés de vecteur pour le vecteur
        foreach ($validatedData['vector_keys'] as $keyData) {
            $vectorKey = VectorKey::create([
                'value' => $keyData['value'],
                'type' => $keyData['type'],
                'name' => $keyData['name'],
                'vector_id' => $vector->id,
            ]);
        }

        // Création des éléments de rapport
        foreach ($validatedData['report_items'] as $itemData) {
            $reportItem = ReportItem::create([
                'report_id' => $report->id,
                'metric_type_id' => $itemData['metric_type_id'],
                'value' => $itemData['value'],
            ]);
        }

        return response()->success($report, __('Reports charged successfully'), 200);
    }

    public function show($zoneId)
    {
        $report = Report::with('items.report', 'creator', 'vector.vectorKeys')->where('zone_id', $zoneId)->first();
        // Charger les éléments associés au rapport

        return response()->success($report, __('Report details retrieved successfully'), 200);
    }

    public function update(ReportRequest $request, Report $report)
    {
        $validatedData = $request->validated();

        // Vérifier si l'utilisateur authentifié est autorisé à mettre à jour le rapport
        if (Auth::user()->id !== $report->user_id) {
            return response()->errors([], __('Unauthorized'), 401);
        }

        // Mettre à jour les champs du rapport avec les données validées
        $report->update([
            'zone_id' => $validatedData['zone_id'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        // Enregistrer l'image si elle est fournie
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('report_images');
            $report->image = $imagePath;
            $report->save();
        }

        // Mettre à jour le vecteur associé au rapport
        $vector = $report->vector;
        if ($vector) {
            $vector->update([
                'path' => $imagePath,
                'category' => $validatedData['type'],
                'type' => $request->file('image')->getClientMimeType(),
            ]);
        }

        // Supprimer les clés de vecteur existantes et en créer de nouvelles
        $vector->keys()->delete();
        foreach ($validatedData['vector_keys'] as $keyData) {
            $vectorKey = VectorKey::create([
                'value' => $keyData['value'],
                'type' => $keyData['type'],
                'name' => $keyData['name'],
                'vector_id' => $vector->id,
            ]);
        }

        // Supprimer les éléments de rapport existants et en créer de nouveaux
        $report->items()->delete();
        foreach ($validatedData['report_items'] as $itemData) {
            $reportItem = ReportItem::create([
                'report_id' => $report->id,
                'metric_type_id' => $itemData['metric_type_id'],
                'value' => $itemData['value'],
            ]);
        }

        return response()->success($report, __('Report updated successfully'), 200);
    }

    public function destroy(Report $report)
    {
        // Vérifier si l'utilisateur authentifié est autorisé à supprimer le rapport
        $user = Auth::user();
        if (!$user->hasRole('Admin') && $user->id !== $report->user_id) {
            return response()->errors([], __('Unauthorized'), 401);
        }

        // Supprimer le rapport
        $report->delete();

        return response()->success([], __('Report deleted successfully'), 200);
    }
}
