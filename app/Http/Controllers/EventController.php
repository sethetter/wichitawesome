<?php

namespace ICT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use ICT\Event;
use ICT\Http\Requests\AdminRequest;
use ICT\Http\Requests\CollectRequest;
use ICT\Http\Requests\StoreRequest;
use ICT\Http\Requests\UpdateRequest;
use ICT\Http\Requests\DestroyRequest;
use ICT\Http\Controllers\Controller;
use ICT\Venue;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function admin(AdminRequest $request)
    {
        $data['events'] = Event::with('venue')->withHidden()->upcoming()->get();
        return view('events.admin', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data['events'] = Event::with('venue')->upcoming()->simplePaginate(10);
        return view('events.index', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getEvents()
    {
        return response()->json(Event::with('venue')->upcoming()->simplePaginate(10));
    }

    public function viewEvents()
    {
        $data['events'] = Event::with('venue')->upcoming()->simplePaginate(10);
        if(count($data['events']))
        {
            return response()->view('events.partials.list', $data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function submit(Request $request)
    {
        $data['venues'] = Venue::all();
        $data['fb_url'] = $request->fb_url;
        dd($request->fb_url);
        return view('events.submit', $data);
    }

    /**
     * Store a newly created resource in storage for approval.
     *
     * @return Response
     */
    public function collect(CollectRequest $request)
    {
        $data = $request->all();
        if($request->user() && $request->user()->hasPermission('events.store')) {
            $data['visible'] = true;
        }

        if(!$request->has('venue_id'))
        {
            $data['venue']['visible'] = true;
            $venue = Venue::firstOrCreate($data['venue']);
            $data['venue_id'] = $venue->id;
        }

        $event = Event::create($data);

        if($event)
        {
            $data = [
                'id'          => $event->id,
                'name'        => $event->name,
                'time'        => $event->start_time->format('g:ia, m/d/Y').(isset($event->end_time) ?  ' - '.$event->end_time->format('g:ia, m/d/Y') : ''),
                'location'    => (isset($venue)) ? $venue->name : Venue::find($event->venue_id)->name,
                'facebook'    => $event->facebook,
                'description' => $event->description,
            ];

            Mail::send('emails.events.collect', $data, function($message)
            {
                $message->from('noreply@wichitaweso.me', 'Wichitasome!');
                $message->to('christianbtaylor@gmail.com')->subject('New Event!');
            });
        }
        return redirect('events/submit')->with('message', '<strong>Woo! A new event!</strong> Thanks, we\'ll look it over at get it listed ASAP.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(AdminRequest $request)
    {
        $data['venues'] = Venue::all();
        return view('events.create', $data);
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
        Event::create($data);
        return redirect('events/admin')->with('message', 'Event created!');
    }

    /**
     * Return event info with API.
     *
     * @return Response
     */
    public function getEvent(Request $request, $id)
    {
        return response()->json(Event::find($id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $data['event'] = Event::with('venue')->findOrFail($id); 
        return view('events.show', $data);
    }

    /**
     * Export an event to ical.
     *
     * @return Response
     */
    public function export($id)
    {
        $event = Event::find($id);
        return response()->view('events.export', compact('event'))
                ->header('Content-Type', 'text/calendar; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename=' .str_slug($event->name). '.ics');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(AdminRequest $request, $id)
    {
        $data['event'] = Event::withHidden()->findOrFail($id);
        $data['venues'] = Venue::all();
        return view('events.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        Event::withHidden()->findOrFail($id)->update($request->all());
        return redirect('events/admin')->with('message', 'Event updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(DestroyRequest $request, $id)
    {
        $event = Event::withHidden()->findOrFail($id);
        $event->delete();
        return redirect('events/admin')->with('message', 'Event destroyed.');
    }
}
