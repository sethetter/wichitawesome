@extends('app')

@section('title', 'Events')
@section('description', '')

@section('container', '')

@section('content')
    <div class="mb2">
        <a class="btn caps bg-green" href="{{ action('EventController@create') }}">New Event</a>
    </div>
    <div class="col-12 o-auto h5">
        <table>
            <thead>
                <tr>
                    <th scope="col" class="h6">#<span class="sr-only"> ID</span></th>
                    <th scope="col" class="h6">Name</th>
                    <th scope="col" class="h6">Start Time</th>
                    <th scope="col" class="h6">End Time</th>
                    <th scope="col" class="h6">Venue</th>
                    <th scope="col" class="h6">Facebook</th>
                    <th scope="col" class="h6">Hashtag</th>
                    <th scope="col" class="h6">User</th>
                    <th scope="col" class="h6">Visibiliy</th>
                    <th scope="col" class="h6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td><a href="{{ action('EventController@edit', $event->id) }}">{{ $event->name }}</a></td>
                        <td class="nowrap">{{ $event->start_time->format('m/d/Y g:i A') }}</td>
                        <td class="nowrap">{{ $event->end_time ? $event->end_time->format('m/d/Y g:i A') : '' }}</td>
                        <td><a href="{{ action('VenueController@edit', $event->venue->id) }}">{{ $event->venue->name }}</a></td>
                        <td class="center tbl-cell">
                            @if($event->facebook)
                                <a target="_blank" href="https://facebook.com/events/{{ $event->facebook }}"><svg class="i"><use xlink:href="#icon-launch"></use></svg></a>
                            @endif
                        </td>
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
                        <td class="center tbl-cell">
                            @if($event->visible)
                                <a target="_blank" href="{{ action('EventController@show',$event->id) }}"><svg class="green i"><use xlink:href="#icon-visibility"></use></svg></a>
                            @else
                                <svg class="red i"><use xlink:href="#icon-visibility-off"></use></svg>
                            @endif
                        </td>
                        <td class="nowrap center">
                            <form class="inl-blk" method="post" action="{{ action('EventController@destroy',$event->id) }}" onsubmit="return confirm('You definitely want to delete this event?');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" class="dark-red" style="padding:0;background:none;"><svg class="i"><use xlink:href="#icon-bomb"></use></svg></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection