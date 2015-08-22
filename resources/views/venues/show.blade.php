@extends('app')

@section('title', $venue->name or $venue->address())
@section('description', '')

@section('container', 'container')

@section('content')
    <div id="map" style="width:500px;height:500px;"></div>
    @include('venues.single', compact('venue'))
@endsection