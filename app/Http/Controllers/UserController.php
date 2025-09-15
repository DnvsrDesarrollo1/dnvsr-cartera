<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')
            ->orderBy('name', 'asc')
            ->get();

        $roles = Role::orderBy('name', 'ASC')
            ->get();

        $permissions = Permission::orderBy('name', 'ASC')
            ->get();

        $activeUsers = \Illuminate\Support\Facades\DB::table(config('session.table'))
            ->distinct()
            ->select(['users.id', 'users.name', 'users.email'])
            ->whereNotNull('user_id')
            ->leftJoin('users', config('session.table') . '.user_id', '=', 'users.id')
            ->get()
            ->count();

        return view('users.index', compact('users', 'roles', 'permissions', 'activeUsers'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user->syncRoles($request->role);

        return back()->with('success', 'Rol actualizado correctamente');
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user->syncPermissions($request->permissions);

        return back()->with('success', 'Permisos actualizados correctamente');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $proyectos = Project::where('user_id', null)
            ->orWhere('user_id', $user->id)
            ->orderBy('nombre_proyecto', 'asc')
            ->get();

        return view('users.edit', compact('user', 'roles', 'proyectos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //return $request;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'proyectos' => 'nullable|array',
            'proyectos.*' => 'exists:projects,id'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        // Save user changes
        $user->save();

        // Sync projects relationship
        if ($request->has('proyectos')) {
            // Update user_id in projects table using foreach
            foreach ($request->proyectos as $projectId) {
                Project::where('id', $projectId)
                    ->update(['user_id' => $user->id]);
            }
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'Usuario eliminado correctamente');
    }

    public function logoutAll()
    {
        \Illuminate\Support\Facades\DB::table('sessions')->truncate();

        return back();
    }
}
