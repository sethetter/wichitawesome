<?php

namespace ICT\Http\Controllers;

use Illuminate\Http\Request;

use ICT\Organization;
use ICT\Tag;
use ICT\Http\Requests\AdminRequest;
use ICT\Http\Requests\CollectRequest;
use ICT\Http\Requests\StoreRequest;
use ICT\Http\Requests\UpdateRequest;
use ICT\Http\Requests\DestroyRequest;
use ICT\Http\Controllers\Controller;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function admin(AdminRequest $request)
    {
        $data['organizations'] = Organization::withHidden()->get();
        return view('organizations.admin', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data['organizations'] = Organization::with('tags')->get();
        return view('organizations.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AdminRequest $request)
    {
        $data['tags'] = Tag::orderBy('name')->get();
        return view('organizations.create', $data);
    }

    /**
     * Show the form for submitting a new resource.
     *
     * @return Response
     */
    public function submit(Request $request)
    {
        $data['fb_url'] = $request->fb_url;
        $data['tags'] = Tag::orderBy('name')->get();
        return view('organizations.submit', $data);
    }

    /**
     * Store a newly created resource in storage for approval.
     *
     * @return Response
     */
    public function collect(CollectRequest $request)
    {
        $data = $request->all();
        if($request->user() && $request->user()->hasPermission('organizations.admin')) {
            $data['visible'] = true;
        }
        Organization::create($data)->tags()->attach($request->tags);
        return redirect('organizations.submit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        $data = $request->all();
        $data['visible'] = true;
        Organization::create($data)->tags()->attach($request->tags);
        return redirect('organizations/admin')->with('message', 'Organization created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $data['organization'] = Organization::with('tags')->findOrFail($id);
        return view('organizations.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(AdminRequest $request, $id)
    {
        $data['organization'] = Organization::with('tags')->withHidden()->findOrFail($id);
        $data['tags'] = Tag::orderBy('name')->get();
        return view('organizations.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $organization = Organization::withHidden()->findOrFail($id);
        $organization->update($request->all());
        $organization->tags()->sync($request->input('tags', []));
        return redirect('organizations/admin')->with('message', 'Organization updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(DestroyRequest $request, $id)
    {
        $organization = Organization::withHidden()->findOrFail($id);
        if(count($event->tags)) {
            $organization->tags()->detach($events->tags->lists('id')->toArray());
        }
        $organization->delete();
        return redirect('organizations/admin')->with('message', 'Organization destroyed.');
    }
}
