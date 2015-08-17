@extends('app')

@section('title', 'Venues')
@section('description', '')

@section('container', '')

@section('content')
    <div class="mb2">
        <a class="btn caps bg-green" href="{{ action('VenueController@create') }}">New Venue</a>
    </div>
    <div class="table-container">
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
                        <td>{{ $venue->facebook }}</td>
                        <td>{{ $venue->twitter }}</td>
                        <td>{{ $venue->website }}</td>
                        <td>{{ $venue->email }}</td>
                        <td class="nowrap">{{ $venue->phone }}</td>
                        <td class="center">
                            @if($venue->visible)
                                <span class="green i i-visibility h2"></span>
                            @else
                                <span class="red i i-visibility-off h2"></span>
                            @endif
                        </td>
                        <td class="nowrap">
                            <form class="inl-blk" method="post" action="{{  action('VenueController@destroy',$venue->id) }}" onsubmit="return confirm('You definitely want to delete this venue?');">
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