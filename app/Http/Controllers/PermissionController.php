<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permissions = Permission::with('roles')
            ->get();
        return Inertia::render("Permissions/Index", [
            "roles" => Role::all(),
            "permissions" => $permissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Permissions/Create", [
            "permissions" => Permission::all(),
            "roles" => Role::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'roles' => 'nullable|array',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
        ]);
        $permission->syncRoles($request->roles);

        return to_route("permissions.index")->with("message", "Success Create Permission");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);
        return Inertia::render("Permissions/Edit", [
            "permission" => $permission,
            "rolePermissions" => $permission->roles->pluck("name")->all(),
            "roles" => Role::all()
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
            'roles' => 'nullable',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->save();
        $permission->syncRoles($request->roles);

        return to_route("permissions.index")->with("message", "Success Create ermission");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        Permission::destroy($id);
        return to_route("permissions.index")->with("message", "Success Delete Permission");
    }
        public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        // dd($ids);
        if (!empty($ids)) {
            Permission::whereIn('id', $ids)->delete();
        }

        return redirect()->back(303)->with('success', 'Selected users deleted successfully');
    }
}
