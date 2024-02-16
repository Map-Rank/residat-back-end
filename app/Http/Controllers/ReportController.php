<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

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
}
