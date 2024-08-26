<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sectors = Sector::withCount('posts')->get();
        return view('sectors.index', compact('sectors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Sector::create($validatedData);

        return redirect()->route('sectors.index')->with('success', 'Sector created successfully.');
    }

    /**
     * @codeCoverageIgnore
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        return view('sectors.show', compact('sector'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sector $sector)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $sector->update($validatedData);

        return redirect()->route('sectors.index')->with('success', 'Sector updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        $sector->delete();

        return redirect()->route('sectors.index')->with('success', 'Sector deleted successfully.');
    }
}
