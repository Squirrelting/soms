<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        
        $roles = Role::select('id', 'name')
        ->when($request->searchTerm, function ($query) use ($request){
            return $query->where('name', 'like', '%' . $request->searchTerm . '%');
        })->paginate(10)->withQueryString();

        $roles->transform(function ($role, $key) use ($roles) {
            $role->number = $roles->firstItem() + $key;
            return $role;
        });

        return Inertia::render('Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function edit($id)
    {   
        $permissions = Permission::all();
        $role = Role::with('permissions')->where('id', $id)->first();

        if($role->name === 'admin' || $role->name === 'super-admin'){
            return abort(404);
        }

       

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        DB::beginTransaction();
        $role= Role::findOrFail($id);
        try {
            if($role->hasPermissionTo($request->name)){
                $role->revokePermissionTo($request->name);

                DB::commit();


                return Redirect::route('users.roles-permissions.roles.edit', ['id' => $id])->with('successMessage','Permission: ' . $request->name . ' revoked to role');
            } else {
                $role->givePermissionTo($request->name);

                DB::commit();



                return Redirect::route('users.roles-permissions.roles.edit', ['id' => $id])->with('successMessage', 'Permission: ' . $request->name . ' added to role');
            }
        } catch (Exception $e) {
            // Rollback the transaction if an exception occurred
            DB::rollBack();

            // Return back with error message
            return back()->withErrors($e->getMessage());
        }
    }
}