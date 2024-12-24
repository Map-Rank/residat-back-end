<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Http\Resources\PackageResource;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::where('is_active', true)
            ->orderBy('created_at', 'desc')->get();

        return response()->success(PackageResource::collection($packages), __('Packages charged successfully'), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $package = Package::where('is_active', true)->find($id);

        if (!$package) {
            return response()->errors([], __('Package not found or inactive'), 404);
        }

        return response()->success(new PackageResource($package), __('Package retrieved successfully'), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageRequest $request)
    {
        $validatedData = $request->validated();
        $package = Package::create($validatedData);

        return response()->success(new PackageResource($package), __('Package created successfully'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PackageRequest $request, Package $package)
    {
        $validatedData = $request->validated();
        $package->update($validatedData);

        return response()->success(new PackageResource($package), __('Package updated successfully'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        $package->delete();
        return response()->success([], __('Package deleted successfully'), 200);
    }
}
