<?php

namespace ICT\Http\Controllers;

use Illuminate\Http\Request;

use ICT\Venue;
use ICT\Http\Requests\AdminRequest;
use ICT\Http\Requests\CollectRequest;
use ICT\Http\Requests\StoreRequest;
use ICT\Http\Requests\UpdateRequest;
use ICT\Http\Requests\DestroyRequest;
use ICT\Http\Controllers\Controller;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function admin(AdminRequest $request)
    {
        $data['venues'] = Venue::withHidden()->get();
        return view('venues.admin', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data['venues'] = Venue::all();
        return view('venues.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AdminRequest $request)
    {
        return view('venues.create');
    }

    /**
     * Show the form for submitting a new resource.
     *
     * @return Response
     */
    public function submit()
    {
        return view('venues.submit');
    }

    /**
     * Store a newly created resource in storage for approval.
     *
     * @return Response
     */
    public function collect(CollectRequest $request)
    {
        $data = $request->all();
        if($request->user() && $request->user()->hasPermission('venues.store')) {
            $data['visible'] = true;
        }
        Venue::create($data);
        return redirect('venues.submit');
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
        Venue::create($data);
        return redirect('venues/admin')->with('message', 'Venue created!');
    }

    /**
     * Return event info with API.
     *
     * @return Response
     */
    public function getVenue(Request $request, $id)
    {
        return response()->json(Venue::find($id));
    }

    /**
     * Search venues by name and street with API.
     *
     * @return Response
     */
    public function getVenuesByLocation(Request $request)
    {
        $query = $request->get('query');
        $venues = Venue::where('name', 'like', "%$query%")->orWhere('street', 'like', "%$query%")->get();
        return response()->json($venues);
    }

    /**
     * Search venues given keys.
     *
     * @return Response
     */
    public function getVenues(Request $request)
    {
        $venues = Venue::query();
        foreach ($request->all() as $key => $value) {
            $venues->where($key, '=', $value);
        }
        $results = $venues->get();
        return response()->json($results);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $data['venue'] = Venue::findOrFail($id); 
        return view('venues.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(AdminRequest $request, $id)
    {
        $data['venue'] = Venue::withHidden()->findOrFail($id);
        return view('venues.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        Venue::withHidden()->findOrFail($id)->update($request->all());
        return redirect('venues/admin')->with('message', 'Venue updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(DestroyRequest $request, $id)
    {
        Venue::withHidden()->findOrFail($id)->delete();
        return redirect('venues/admin')->with('message', 'Venue destroyed.');
    }
}
