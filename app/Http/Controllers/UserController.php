<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('zone')->paginate(100); // 100 utilisateurs par page, ajustez selon vos besoins
        return view('users.index', ['users' => $users]);
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
    public function store(UserRequest $request)
    {
        $user = User::create($request->all());

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id)->loadMissing('interactions.typeInteraction', 'zone', 'myPosts.medias', 'myPosts.postComments');
        // $user->loadMissing('interactions.typeInteraction', 'zone', 'myPosts.medias', 'myPosts.postComments');
        return view('users.show', ['user' => $user]);
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
    public function update(UserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès');
    }

    /**
     * Ban a user
     */
    public function banUser($id)
    {

        $user = User::find($id);

        if(!$user){
            return redirect()->back()->with('success', 'User is banned successfully');
        }

        if ($user->active) {
            $user->update([
                'active' => false,
                'activated_at' => null,
            ]);

            return redirect()->back()->with('success', 'User is banned successfully');
        } else {
            return redirect()->back()->with('warning', 'User is already banned');
        }
    }

    /**
     * Active a user
     */
    public function activeUser($id)
    {

        $user = User::find($id);

        if(!$user){
            return redirect()->back()->with('success', 'User is banned successfully');
        }

        if (!$user->active) {
            $user->update([
                'active' => true,
                'activated_at' => Carbon::now(),
            ]);

            // Optional: Add additional actions after banning, such as sending notifications or logging the action

            return redirect()->back()->with('success', 'User is activated successfully');
        } else {
            return redirect()->back()->with('warning', 'User is already activated');
        }
    }
}
