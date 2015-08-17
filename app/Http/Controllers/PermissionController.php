<?php

namespace ICT\Http\Controllers;

use Illuminate\Http\Request;

use Cache;
use ICT\Http\Requests\AdminRequest;
use ICT\Http\Requests\StoreRequest;
use ICT\Http\Requests\UpdateRequest;
use ICT\Http\Requests\DestroyRequest;
use ICT\Http\Controllers\Controller;
use ICT\Permission;
use ICT\Role;

class PermissionController extends Controller
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
        $data['permissions'] = Permission::all();
        return view('permissions.admin', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AdminRequest $request)
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        Permission::create($request->all());
        return redirect('permissions/admin')->with('message', 'Permission created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return view('permissions.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(AdminRequest $request, $id)
    {
        $data['permission'] = Permission::findOrFail($id);
        return view('permissions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        Permission::findOrFail($id)->update($request->all());
        return redirect('permissions/admin')->with('message', 'Permission updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(DestroyRequest $request, $id)
    {
        Permission::findOrFail($id)->delete();
        return redirect('permissions/admin')->with('message', 'Perission destroyed.');
    }
}
