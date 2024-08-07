<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Zone;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['zone', 'postCount'])->paginate(100); // 100 utilisateurs par page, ajustez selon vos besoins
        return view('users.index', ['users' => $users]);
    }

    /**
     * @codeCoverageIgnore
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::where('level_id',4)->get();
        $roles = Role::all();
        return view('users.create', compact('zones','roles'));
    }

    /**
     * @codeCoverageIgnore
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $request['password'] = Str::random(8);
        $user = User::create($request->all());
        
        // Envoyer un e-mail à l'utilisateur
        Mail::to($user->email)->send(new WelcomeEmail($user, $request['password']));

        $user->password = bcrypt($request['password']);

        // Sauvegarder l'utilisateur avec le mot de passe généré
        $user->save();

        // Récupérer l'ID du rôle envoyé depuis le formulaire
        $role_id = $request->input('role_id');

        // Vérifier si l'ID du rôle est valide
        if ($role_id) {
            // Récupérer le rôle correspondant à l'ID
            $role = Role::findById($role_id);
            
            // Vérifier si le rôle existe
            if ($role) {
                // Attribuer le rôle à l'utilisateur
                $user->assignRole($role);
            } else {
                // Gérer le cas où le rôle n'existe pas
                return redirect()->back()->with('error', 'Invalid role');
            }
        }

        // Vérifiez si un fichier d'avatar a été téléchargé
        if ($request->hasFile('avatar')) {
            // Récupérez le fichier d'avatar téléchargé
            $avatar = $request->file('avatar');
            
            // Générez un nom de fichier unique pour l'avatar
            $avatarName = uniqid('avatar_') . '.' . $avatar->getClientOriginalExtension();

            // Stockez l'avatar dans le dossier de stockage
            $avatarPath = $avatar->storeAs('media/avatat/'.$user->email, $avatarName, 's3');

            // Mettez à jour l'attribut d'avatar de l'utilisateur avec le chemin d'accès au fichier
            $user->avatar = $avatarPath;

            // Sauvegardez les modifications apportées à l'utilisateur
            $user->save();
        }

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
     * @codeCoverageIgnore
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $zones = Zone::where('level_id',4)->get();
        $roles = Role::all();

        return view('users.edit', compact('user','zones','roles'));
    }

    /**
     * @codeCoverageIgnore
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        // Récupérer l'ID du rôle envoyé depuis le formulaire
        $role_id = $request->input('role_id');

        // Vérifier si l'ID du rôle est valide
        if ($role_id) {
            // Récupérer le rôle correspondant à l'ID
            $role = Role::findById($role_id);
            
            // Vérifier si le rôle existe
            if ($role) {
                // Attribuer le rôle à l'utilisateur
                $user->assignRole($role);
            } else {
                // Gérer le cas où le rôle n'existe pas
                return redirect()->back()->with('error', 'Invalid role');
            }
        }

        // Vérifiez si un fichier d'avatar a été téléchargé
        if ($request->hasFile('avatar')) {
            // Récupérez le fichier d'avatar téléchargé
            $avatar = $request->file('avatar');
            
            // Générez un nom de fichier unique pour l'avatar
            $avatarName = uniqid('avatar_') . '.' . $avatar->getClientOriginalExtension();

            // Stockez l'avatar dans le dossier de stockage
            $avatarPath = $avatar->storeAs('storage/media/avatar/'.$user->email, $avatarName, 's3');

            // Mettez à jour l'attribut d'avatar de l'utilisateur avec le chemin d'accès au fichier
            $user->avatar = $avatarPath;

            // Sauvegardez les modifications apportées à l'utilisateur
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * @codeCoverageIgnore
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
