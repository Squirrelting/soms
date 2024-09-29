<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionUpdateRequest;
use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\PermissionStoreRequest;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $permissions = Permission::select('id', 'name')
        ->when($request->searchTerm, function ($query, $searchTerm){
            return $query->where('name', 'like', '%' . $searchTerm . '%');
        })->paginate(10)->withQueryString();

        $permissions->transform(function ($permission, $key) use ($permissions) {
            $permission->number = $permissions->firstItem() + $key;
            return $permission;
        });

        return Inertia::render('Permissions/Index',[
            'permissions' => $permissions
        ]);
    }

    public function add()
    {
        return Inertia::render('Permissions/Add');
    }

    public function store(PermissionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $newPermission = Permission::create($validated);


            // Commit the transaction as successful
            DB::commit();

            // Redirect to the index page with success message and newly added customer data
            return Redirect::route('users.roles-permissions.permissions.index')->with('successMessage', 'New Role permission successfully.');
        } catch (Exception $e) {
            // Rollback the transaction if an exception occurred
            DB::rollBack();

            // Return back with error message
            return back()->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return Inertia::render('Permissions/Edit', [
            'permission' => $permission
        ]);
    }

    public function update(PermissionUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $permission = Permission::findOrFail($id);

            $permission->update($validated);

            // Commit the transaction as successful
            DB::commit();


            // Redirect to the index page with success message and newly added customer data
            return Redirect::route('users.roles-permissions.permissions.index')->with('successMessage', 'Permission updated successfully.');
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

            $permission = Permission::findOrFail($id);

            $permission->delete();
            // Commit the transaction as successful
            DB::commit();

            // Redirect to the index page with success message and newly added customer data
            return Redirect::route('users.roles-permissions.permissions.index')->with('successMessage', 'Permission deleted successfully.');
        } catch (Exception $e) {
            // Rollback the transaction if an exception occurred
            DB::rollBack();

            // Return back with error message
            return back()->withErrors($e->getMessage());
        }
    }
}