@extends('app')

@section('title', 'Venues')
@section('description', '')

@section('container', 'container')

@section('content')
    @if( count($venues) )
        @foreach($venues as $venue)
            @include('venues.single', compact($venue))
        @endforeach
    @else
        <div>
            <strong>Ohhhh shit</strong>...it looks like there are no venues. Shoot me an email and bug me to add some.
        </div>
    @endif
@endsection