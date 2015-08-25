@extends('app')

@section('title', 'Venues')
@section('description', '')

@section('container', '')

@section('content')
    <div class="mb2">
        <a class="btn caps bg-green" href="{{ action('VenueController@create') }}">New Venue</a>
    </div>
    <div class="col-12 o-auto h5">
        <table>
            <thead>
                <tr>
                    <th>#<span class="sr-only"> ID</span></th>
                    <th>Name</th>
                    <th>Steet</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip</th>
                    <th>Facebook</th>
                    <th>Twitter</th>
                    <th>Website</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Visibiliy</th>
                    <th><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venues as $venue)
                    <tr>
                        <td>{{ $venue->id }}</td>
                        <td><a href="{{ action('VenueController@edit', $venue->id) }}">{{ $venue->name }}</a></td>
                        <td>{{ $venue->street }}</td>
                        <td>{{ $venue->city }}</td>
                        <td>{{ $venue->state }}</td>
                        <td>{{ $venue->zip }}</td>
                        <td class="center">
                            @if($venue->facebook)
                                <a target="_blank" href="https://facebook.com/events/{{ $venue->facebook }}"><svg class="i"><use xlink:href="#icon-launch"></use></svg></a>
                            @endif
                        </td>
                        <td>
                            @if($venue->twitter)
                                <a target="_blank" href="https://twitter.com/{{ $venue->twitter }}">{{ '@'.$venue->twitter }}</a>
                            @endif
                        </td>
                        <td>
                            @if($venue->website)
                                <a target="_blank" href="{{ $venue->website }}">{{ $venue->website }}</a>
                            @endif
                        </td>
                        <td class="center">
                            @if($venue->email)
                                <a target="_blank" href="mailto:{{ $venue->email }}"><svg class="i"><use xlink:href="#icon-mail"></use></svg></a>
                            @endif
                        </td>
                        <td class="nowrap">
                            @if($venue->phone)
                                <a target="_blank" href="tel:{{ $venue->phone }}">{{ $venue->phone }}</a>
                            @endif
                        </td>
                        <td class="center">
                            @if($venue->visible)
                                <a target="_blank" href="{{ action('VenueController@show',$venue->id) }}"><svg class="green i"><use xlink:href="#icon-visibility"></use></svg></a>
                            @else
                                <svg class="red i"><use xlink:href="#icon-visibility-off"></use></svg>
                            @endif
                        </td>
                        <td class="nowrap">
                            <form class="inl-blk" method="post" action="{{  action('VenueController@destroy',$venue->id) }}" onsubmit="return confirm('You definitely want to delete this venue?');">
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