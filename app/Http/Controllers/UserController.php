<?php

namespace ICT\Http\Controllers;

use Illuminate\Http\Request;

use ICT\Http\Requests\AdminRequest;
use ICT\Http\Requests\StoreRequest;
use ICT\Http\Requests\UpdateRequest;
use ICT\Http\Requests\DestroyRequest;
use ICT\Http\Controllers\Controller;
use ICT\Role;
use ICT\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function admin(AdminRequest $request)
    {
        $data['users'] = User::with('role')->get();
        return view('users.admin', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AdminRequest $request)
    {
        $data['roles'] = Role::all();
        return view('users.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        User::create($request->all());
        return redirect('users/admin')->with('message', 'User created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return view('users.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(AdminRequest $request, $id)
    {
        $data['user'] = User::findOrFail($id);
        $data['roles'] = Role::all();
        return view('users.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $userInput = $request->all();
        User::findOrFail($id)->update($userInput);
        return redirect('users/admin')->with('message', 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(DestroyRequest $request, $id)
    {
        User::findOrFail($id)->delete();
        return redirect('users/admin')->with('message', 'User destroyed.');
    }
}
