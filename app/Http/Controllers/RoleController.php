<?php

namespace ICT\Http\Controllers;

use Illuminate\Http\Request;

use Cache;
use ICT\Http\Requests\AdminRequest;
use ICT\Http\Requests\UpdateRequest;
use ICT\Http\Requests\DestroyRequest;
use ICT\Http\Controllers\Controller;
use ICT\Permission;
use ICT\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function admin(AdminRequest $request)
    {
        // Clear all permission caches
        $roles = Role::all();
        foreach($roles as $role)
        {
            Cache::forget($role->slug);
        }
        $data['roles'] = Role::with('users', 'permissions')->get();
        return view('roles.admin', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AdminRequest $request)
    {
        $data['permissions'] = Permission::all();
        return view('roles.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(AdminRequest $request)
    {
        Role::create($request->all())->permissions()->sync($request->permissions);
        return redirect('roles/admin')->with('message', 'Role created!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(AdminRequest $request, $id)
    {
        $data['permissions'] = Permission::all();
        $data['role'] = Role::findOrFail($id);
        return view('roles.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->all());
        $role->permissions()->sync($request->permissions);
        return redirect('roles/admin')->with('message', 'Role updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(DestroyRequest $request, $id)
    {
        Role::findOrFail($id)->delete();
        return redirect('roles/admin')->with('message', 'Role destroyed.');
    }
}
