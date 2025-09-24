<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::with('permissions')
            ->get();
        return Inertia::render("Dashboard/Roles/RolesDataTables", [
            "roles" => $roles,
            "permissions" => Permission::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Dashboard/Roles/RolesCreate", [
            "permissions" => Permission::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);
        $role->syncPermissions($request->permissions);

        return to_route("roles.index")->with("message", "Success Create Role");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Inertia::render("Dashboard/Roles/RolesShow", [
            "role" => Role::findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return Inertia::render("Dashboard/Roles/RolesEdit", [
            "role" => $role,
            "rolePermissions" => $role->permissions->pluck("name")->all(),
            "permissions" => Permission::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'permissions' => 'nullable',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();
        $role->syncPermissions($request->permissions);

        return to_route("roles.index")->with("message", "Success Create Role");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === "Super Admin") {
            abort(403, 'You are not allowed to delete Super Admin Role.');
        };


        Role::destroy($id);
        return to_route("roles.index")->with("message", "Success Delete Role");
    }
        public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        // dd($ids);
        if (!empty($ids)) {
            Role::whereIn('id', $ids)->delete();
        }

        return redirect()->back(303)->with('success', 'Selected roles deleted successfully');
    }
}
