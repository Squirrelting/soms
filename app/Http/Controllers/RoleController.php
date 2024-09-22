<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleUpdateRequest;
use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RoleStoreRequest;
use Illuminate\Support\Facades\Redirect;

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

    public function add()
    {
        return Inertia::render('Roles/Add');
    }

    public function edit($id)
    {
        $permissions = Permission::all();
        $role = Role::with('permissions')->where('id', $id)->first();

       

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function store(RoleStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $newRole = Role::create($validated);

            // Commit the transaction as successful
            DB::commit();



            // Redirect to the index page with success message and newly added customer data
            return Redirect::route('users.roles-permissions.roles.index')->with('successMessage', 'New Role added successfully.');
        } catch (Exception $e) {
            // Rollback the transaction if an exception occurred
            DB::rollBack();

            // Return back with error message
            return back()->withErrors($e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $role = Role::findOrFail($id);

            $role->delete();
            // Commit the transaction as successful
            DB::commit();


            // Redirect to the index page with success message and newly added customer data
            return Redirect::route('users.roles-permissions.roles.index')->with('successMessage', 'Role deleted successfully.');
        } catch (Exception $e) {
            // Rollback the transaction if an exception occurred
            DB::rollBack();

            // Return back with error message
            return back()->withErrors($e->getMessage());
        }
    }

    public function update(RoleUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $role = Role::findOrFail($id);

            $role->update($validated);

            // Commit the transaction as successful
            DB::commit();


            // Redirect to the index page with success message and newly added customer data
            return Redirect::route('users.roles-permissions.roles.index')->with('successMessage', 'Role updated successfully.');
        } catch (Exception $e) {
            // Rollback the transaction if an exception occurred
            DB::rollBack();

            // Return back with error message
            return back()->withErrors($e->getMessage());
        }
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