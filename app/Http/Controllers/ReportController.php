<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function  index(Request $request){
        $reports = Report::query()->paginate(50);
        return view('reports.index', compact('reports'));
    }

    public function create(){
        $types = ["DROUGHT", "FLOOD", "WATER_STRESS"];

        return view('reports.create', compact('types'));
    }

    public function store(ReportRequest $request){
        $validatedData = $request->validated();

        $user = Auth::user();

        $report = Report::create([
            'code' => $validatedData['code'],
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
        
        return view('reports.show', compact('report'));
    }
}
