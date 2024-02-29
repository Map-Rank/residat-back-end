<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZoneRequest;
use App\Http\Resources\ZoneResource;
use App\Models\Level;
use App\Models\Zone;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function index(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string'],
            'parent_id'=> ['sometimes', 'int'],
            'level_id'=> ['sometimes', 'int'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(), __('Bad parameters'), 400);
        }

        $validated = $validator->validated();
        $data = Zone::with('children', 'parent');
        if(isset($validated['name'])){
            $data = $data->where('name', 'like' , '%'.$validated['name'].'%');
        }

        if(isset($validated['parent_id'])){
            $data = $data->where('parent_id' , $validated['parent_id']);
        }

        $zones = $data->orderBy('level_id')->paginate(100);

        return view('zones.regions', compact('zones'));
    }

    public function divisions(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'parent_id'=> ['sometimes', 'int'],
        ]);

        if ($validator->fails()) {
            // return response()->errors($validator->failed(), __('Bad parameters'), 400);
            return redirect()->back()->with($validator->failed(), 'Bad parameters');
        }

        $validated = $validator->validated();
        $data = Zone::with('children');
        if(isset($validated['name'])){
            $data = $data->where('name', 'like' , '%'.$validated['name'].'%');
        }

        if(isset($validated['parent_id'])){
            $data = $data->where('parent_id' , $validated['parent_id']);
        }

        $divisions = $data->where('parent_id' , $id)->get();

        return view('zones.divisions', compact('divisions'));
    }

    public function subdivisions(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'parent_id'=> ['sometimes', 'int'],
        ]);

        if ($validator->fails()) {
            return response()->errors($validator->failed(), __('Bad parameters'), 400);
        }

        $validated = $validator->validated();
        $data = Zone::with('children');
        if(isset($validated['name'])){
            $data = $data->where('name', 'like' , '%'.$validated['name'].'%');
        }

        if(isset($validated['parent_id'])){
            $data = $data->where('parent_id' , $validated['parent_id']);
        }

        $subdivisions = $data->where('parent_id' , $id)->get();

        return view('zones.subdivisions', compact('subdivisions'));
    }
    /**
     * @codeCoverageIgnore
     */
    public function show($id) {

        $data = Zone::query()->find($id);
        $data->loadMissing(['parent']);

        if (!$data) {
            return redirect()->back()->with('errors', 'Zone not found');
        }

        return view('zones.show', compact('data'));
    }

    /**
     * Create and store a zone
     *
     * @param ZoneRequest $request List of elements used to save a zone entity.
     */
    public function store(ZoneRequest $request)  {

        if($request['division_id'] > 0){
            $parent = Zone::query()->where('parent_id', $request['region_id'])
                ->where('id', $request['division_id'])->first();
        }
        else if($request['region_id'] > 0) {
            $parent = Zone::query()->where('id', $request['region_id'])->first();
        }
        else {
            $parent = Zone::query()->where('name', 'Cameroun')->first();
        }

        $datum = new Zone();
        $datum->parent()->associate($parent);
        $datum->level_id = $request['level_id'];
        $datum->name = $request['name'];

        if ($request->hasFile('data')) {
            $mediaFile = $request->file('data');

            $mediaPath = $mediaFile->store('media/zone', 'public');
            $datum->banner = Storage::url($mediaPath);
        }

        return (!$datum->save())
            ? redirect()->back()->with('Error while creating the zone')
            : redirect()->route('zones.index')->withSuccess('Zone '.$datum->name.' created successfully!');
    }

    /**
     * Update the specified zone
     *
     * @param ZoneRequest $request
     * @param int $id
     */
    public function update(ZoneRequest $request, int $id)
    {
        // dd($request->all());
        $zone = Zone::query()->find($id);
        if(!$zone)
        { return response()->notFoundId(); }

        $parent = null;
        if($request['parent_id'] > 0){
            $parent = Zone::query()->where('parent_id', $request['parent_id'])->first();
        }

        $updated = [];
        if($parent != null) {
            $updated['parent_id'] = $parent->id;
        }

        if(isset($request['name'])) {
            $updated['name'] = $request['name'];
        }

        if ($request->hasFile('data')) {
            $mediaFile = $request->file('data');

            $mediaPath = $mediaFile->store('media/zone', 'public');
            $updated['banner'] = Storage::url($mediaPath);
        }

        return (! $zone->update($request->validated()))
            ? redirect()->back()->with('Zone not found')
            : redirect()->route('zones.index')->withSuccess( __('Zone successfully updated!'));
    }

    /**
     * Delete the specified zone.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $zone = Zone::query()->find($id);

        if (!$zone) {
            return redirect()->back()->with('errors', 'Zone not found');
        }

        $zone->delete();

        return redirect()->back()->with('success', 'Zone successfully deleted!');
    }

    public function create(){
        $levels = Level::query()->get();
        return view('zones.create', compact('levels'));
    }

    public function edit($id){
        $zone = Zone::with('parent')->find($id);
        $zones = null;
        if($zone->parent != null)
            $zones = Zone::query()->where('level_id', $zone->parent->level_id)->get();

        return view('zones.edit', compact('zones', 'zone'));
    }
}
