<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisasterRequest;
use App\Models\Disaster;

class DisasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère tous les désastres et les renvoie à la vue 'disasters.index'
        $disasters = Disaster::all();
        return view('disasters.index', compact('disasters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DisasterRequest $request)
    {
        // Crée un nouveau désastre en utilisant les données validées
        Disaster::create($request->validated());
        return redirect()->route('disasters.index')->with('success', 'Disaster created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Disaster $disaster)
    {
        // Affiche un désastre spécifique
        return view('disasters.show', compact('disaster'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DisasterRequest $request, Disaster $disaster)
    {
        // Met à jour un désastre existant avec les nouvelles données validées
        $disaster->update($request->validated());
        return redirect()->route('disasters.index')->with('success', 'Disaster updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disaster $disaster)
    {
        // Supprime un désastre spécifique
        $disaster->delete();
        return redirect()->route('disasters.index')->with('success', 'Disaster deleted successfully.');
    }
}
