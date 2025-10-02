<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->orderby('id', 'desc')
            ->get();
                $roleQuery = Role::query();
        $auth = auth()->user(); // ini buat kalau user bukan super admin, opsi admin dan super admin ga bakalan ada
        if (!$auth->hasRole('Super Admin')) {
            $roleQuery->whereNot('name', 'Super Admin')->WhereNot('name', 'admin');
        }
        return Inertia::render("Dashboard/Users/UsersDataTables", [
            "users" => $users,
            "roles" => $roleQuery->get(),
            "deletedCount" => User::onlyTrashed()->count(),
        ]);
    }

    public function recycleBin()
    {
        $users = User::onlyTrashed()->with('roles')->get();

        return Inertia::render("Dashboard/Users/UsersRecycleBin", [
            "users" => $users,
            "roles" => Role::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roleQuery = Role::query();
        $user = auth()->user();
        if (!$user->hasRole('Super Admin')) {
            $roleQuery->whereNot('name', 'Super Admin')->WhereNot('name', 'admin');
        }

        return Inertia::render("Dashboard/Users/UsersCreate", [
            "roles" => $roleQuery->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        // if(!auth()->user()->hasRole('Super Admin')){
        //     abort(403, 'User except Super Admin can not make another Super Admin');
        // }
        // if(!auth()->user()->hasRole('Admin')){
        //     abort(403, 'User except Super Admin can not make another Admin');
        // }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/|unique:' . User::class,
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                Password::min(8)->numbers()->symbols()->max(255)->mixedCase(),
                'regex:/^[A-Za-z0-9_\-!@#$%^&*()+=\[\]{}]+$/',
            ],
            'verified_email' => ['nullable', 'boolean'],
            'is_active' => 'boolean',

        ], [
            'name.required' => 'Name is required.',
            'username.unique' => 'Username is already taken.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'email.required' => 'Email is required.',
            'password.required' => 'Password is required.',
            'password.regex' => 'Password cant contain Space.',
            'password.confirmed' => 'Passwords do not match'
        ]);

        if ($request->boolean('verified_email') && $request->verified_email) {
            $email = now();
        } else {
            $email = null;
        }
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' =>  Hash::make($request->password),
            'email_verified_at' => $email,
            'is_active' => $request->boolean('is_active') ? true : false,
        ]);
        $user->syncRoles($request->roles);


        return to_route("users.index")->with("message", "Success Create User");
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return Inertia::render("Dashboard/Users/UsersShow", [
            "user" => [
                'id' => $user->id,
                'avatar' => $user->avatar,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : null,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ],
            "roles" => Role::all(),
            "userRoles" => User::findOrFail($id)->roles->pluck("name")->all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to edit a Super Admin.');
        };
        if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Admin does not allowed to edit another Admin.');
        }
        $roleQuery = Role::query();
        $auth = auth()->user();
        if (!$auth->hasRole('Super Admin')) {
            $roleQuery->whereNot('name', 'Super Admin')->WhereNot('name', 'admin');
        }
        return Inertia::render("Dashboard/Users/UsersEdit", [
            "user" => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'verified_email' => $user->hasVerifiedEmail(),
            ],
            "roles" => $roleQuery->get(),
            "userRoles" => $user->roles->pluck("name")->all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id,)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to edit a Super Admin.');
        };
        if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Admin does not allowed to edit another Admin.');
        }
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'username' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users')->ignore($id),
            ],
            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            // 'password' => [
            //     'required',
            //     'confirmed',
            //     Rules\Password::defaults(),
            //     Password::min(8)->numbers()->symbols()->max(255)->mixedCase(),
            //     'regex:/^[A-Za-z0-9_\-!@#$%^&*()+=\[\]{}]+$/',
            // ],
            'verified_email' => ['nullable', 'boolean'],

        ], [
            'name.required' => 'Name is required.',
            'username.unique' => 'Username is already taken.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'email.required' => 'Email is required.',
            // 'password.required' => 'Password is required.',
            'password.regex' => 'Password cant contain Space.',
            'password.confirmed' => 'Passwords do not match'
        ]);
        if ($request->boolean('verified_email') && $request->verified_email) {
            $user = User::findOrFail($id);
            $user->email_verified_at = now();
            $user->save();
        } else {
            $user = User::findOrFail($id);
            $user->email_verified_at = null;
            $user->save();
        }
        if ($request->filled('password')) {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
        }
        $user = User::findOrFail($id); // cari user berdasarkan ID
        $user->fill($validated);
        $user->save();
        // Cek kalau user login bukan super admin
        if (!auth()->user()->hasRole('Super Admin')) {

            // 1. Cek jika user target punya role admin/super admin
            if ($user->hasAnyRole(['admin', 'Super Admin'])) {
                abort(403, 'You are not allowed to edit another admin or super admin.');
            }

            // 2. Cek jika request ingin memberi role admin/super admin
            if (in_array('admin', (array) $request->roles) || in_array('Super Admin', (array) $request->roles)) {
                abort(403, 'You are not allowed to assign admin or super admin roles.');
            }
        }


        $user->syncRoles($request->roles);
        return back()->with("message", "ini jalan cihuy");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to delete another Admin.');
        };
        if ($user->hasRole('Super Admin')) {
            if (!auth()->user()->hasRole('Super Admin')) {
                abort(403, 'You are not allowed to delete Super Admin.');
            }

            if (auth()->user()->id === $user->id) {
                abort(403, 'Access Forbidden, you cannot delete yourself as Super Admin.');
            }
        };


        User::destroy($id);
        return to_route("users.index")->with("message", "Success Delete User");
    }

    public function toggleStatus(string $id)
    {
        $auth = auth()->user();
        $user = User::findOrFail($id);
        if (!$auth->can('users.toggleStatus')) {
            abort(403, 'You are not allowed to toggle Status Users.');
        }
        $user->is_active = !$user->is_active;
        $user->save();
        return redirect()->back()->with('message', 'Status berhasil diubah.');
    }
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        $user = User::findorFail($ids);
        // dd($ids);
        $users = User::whereIn('id', $ids)->get();

        foreach ($users as $user) {
            if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
                abort(403, 'You are not allowed to delete another Admin.');
            }

            if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
                abort(403, 'You are not allowed to delete Super Admin.');
            }
        }
        if (!empty($ids)) {
            User::whereIn('id', $ids)->delete();
        }

        return redirect()->back(303)->with('success', 'Selected users deleted successfully');
    }
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to restore another Admin.');
        };
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to restore Super Admin.');
        };
        $user->restore();
        return to_route("users.recycleBin")->with("message", "Success Restore User");
    }
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        $user = User::withTrashed()->findorFail($ids);
        // dd($ids);
        $users = User::withTrashed()->whereIn('id', $ids)->get();

        foreach ($users as $user) {
            if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
                abort(403, 'You are not allowed to restore another Admin.');
            }

            if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
                abort(403, 'You are not allowed to restore Super Admin.');
            }
        }
        if (!empty($ids)) {
            User::withTrashed()->whereIn('id', $ids)->restore();
        }

        return redirect()->back(303)->with('success', 'Selected users restored successfully');
    }
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to delete another Admin.');
        };
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'You are not allowed to delete Super Admin.');
        };
        $user->forceDelete();
        return to_route("users.recycleBin")->with("message", "Success Delete Permanently User");
    }
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $user = User::withTrashed()->findorFail($ids);
        // dd($ids);
        $users = User::withTrashed()->whereIn('id', $ids)->get();

        foreach ($users as $user) {
            if ($user->hasRole('admin') && !auth()->user()->hasRole('Super Admin')) {
                abort(403, 'You are not allowed to delete another Admin.');
            }

            if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
                abort(403, 'You are not allowed to delete Super Admin.');
            }
        }
        if (!empty($ids)) {
            User::withTrashed()->whereIn('id', $ids)->forceDelete();
        }

        return redirect()->back(303)->with('success', 'Selected users permanently deleted successfully');
    }
}
