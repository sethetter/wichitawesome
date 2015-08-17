@extends('app')

@section('title', 'Events')
@section('description', '')

@section('container', '')

@section('content')
    <div class="mb2">
        <a class="btn caps bg-green" href="{{ action('EventController@create') }}">New Event</a>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="h6">#<span class="sr-only"> ID</span></th>
                    <th class="h6">Name</th>
                    <th class="h6">Start Time</th>
                    <th class="h6">End Time</th>
                    <th class="h6">Venue</th>
                    <th class="h6">Facebook</th>
                    <th class="h6">Hashtag</th>
                    <th class="h6">User</th>
                    <th class="h6">Visibiliy</th>
                    <th class="h6"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td><a href="{{ action('EventController@edit', $event->id) }}">{{ $event->name }}</a></td>
                        <td class="nowrap">{{ $event->start_time->format('m/d/Y g:i a') }}</td>
                        <td class="nowrap">{{ $event->end_time ? $event->end_time->format('m/d/Y g:i a') : '' }}</td>
                        <td><a href="{{ action('VenueController@edit', $event->venue->id) }}">{{ $event->venue->name }}</a></td>
                        <td class="center"><a class="i i-launch h2" target="_blank" href="https://facebook.com/events/{{ $event->facebook }}"></a></td>
                        <td>
                            @if($event->hashtag)
                                <a target="_blank" href="https://twitter.com/search?q=%23{{ $event->hashtag }}">#{{ $event->hashtag }}</a>
                            @endif
                        </td>
                        <td>
                            @if($event->user)
                                <a href="{{ action('UserController@edit', $event->user->id) }}">{{ $event->user->name }}</a>
                            @endif
                        </td>
                        <td class="center ">
                            @if($event->visible)
                                <span class="green i i-visibility h2"></span>
                            @else
                                <span class="red i i-visibility-off h2"></span>
                            @endif
                        </td>
                        <td class="nowrap">
                            <a class="btn bg-blue p1" target="_blank" href="{{ action('EventController@show',$event->id) }}"><span class="i i-launch h2"></span></a>
                            <form class="inl-blk" method="post" action="{{ action('EventController@destroy',$event->id) }}" onsubmit="return confirm('You definitely want to delete this event?');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" class="btn bg-dark-red p1"><span class="i i-delete h2"></span></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection