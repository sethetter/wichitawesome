@extends('app')

@section('title', 'Organizations')
@section('description', '')

@section('container', 'container')

@section('content')
    @if( count($organizations) )
        @foreach($organizations as $organization)
            @include('organizations.single', compact($organization))
        @endforeach
    @else
        <div>
            <strong>Ohhhh shit</strong>...it looks like there are no organizations. Shoot me an email and bug me to add some.
        </div>
    @endif
@endsection