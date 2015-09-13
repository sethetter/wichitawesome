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
                    <th scope="col" class="h6">#<span class="sr-only"> ID</span></th>
                    <th scope="col" class="h6">Name</th>
                    <th scope="col" class="h6">Steet</th>
                    <th scope="col" class="h6">City</th>
                    <th scope="col" class="h6">State</th>
                    <th scope="col" class="h6">Zip</th>
                    <th scope="col" class="h6 center">Facebook</th>
                    <th scope="col" class="h6">Twitter</th>
                    <th scope="col" class="h6">Website</th>
                    <th scope="col" class="h6 center">Email</th>
                    <th scope="col" class="h6 center">Phone</th>
                    <th scope="col" class="h6 center">Visibiliy</th>
                    <th scope="col" class="h6 center">Actions</th>
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
                        <td class="nowrap">{{ $venue->zip }}</td>
                        <td class="center">
                            @if($venue->facebook)
                                <a target="_blank" href="https://facebook.com/{{ $venue->facebook }}"><svg class="i"><use xlink:href="#icon-launch"></use></svg></a>
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
                        <td class="center tbl-cell">
                            @if($venue->email)
                                <a target="_blank" href="mailto:{{ $venue->email }}"><svg class="i"><use xlink:href="#icon-mail"></use></svg></a>
                            @endif
                        </td>
                        <td class="center tbl-cell">
                            @if($venue->phone)
                                <a target="_blank" href="tel:{{ $venue->phone }}"><svg class="i"><use xlink:href="#icon-smartphone"></use></svg></a>
                            @endif
                        </td>
                        <td class="center tbl-cell">
                            @if($venue->visible)
                                <a target="_blank" href="{{ action('VenueController@show',$venue->id) }}"><svg class="green i"><use xlink:href="#icon-visibility"></use></svg></a>
                            @else
                                <svg class="red i"><use xlink:href="#icon-visibility-off"></use></svg>
                            @endif
                        </td>
                        <td class="nowrap center tbl-cell">
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