<?php

namespace ICT\Http\Controllers;

use Illuminate\Http\Request;

use ICT\Http\Requests\AdminRequest;
use ICT\Http\Requests\StoreRequest;
use ICT\Http\Requests\UpdateRequest;
use ICT\Http\Requests\DestroyRequest;
use ICT\Http\Controllers\Controller;
use ICT\Tag;

class TagController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function admin(AdminRequest $request)
    {
        $data['tags'] = Tag::all();
        return view('tags.admin', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AdminRequest $request)
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        Tag::create($request->all());
        return redirect('tags/admin')->with('message', 'Tag created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $data['event'] = Tag::with('events')->findOrFail($id); 
        return view('tags.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(AdminRequest $request, $id)
    {
        $data['tag'] = Tag::findOrFail($id);
        return view('tags.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        Tag::findOrFail($id)->update($request->all());
        return redirect('tags/admin')->with('message', 'Tag updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(DestroyRequest $request, $id)
    {
        Tag::findOrFail($id)->delete();
        return redirect('tags/admin')->with('message', 'Tag destroyed.');
    }
}
