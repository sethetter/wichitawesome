@extends('app')

@section('title', $organization->name)
@section('description', '')

@section('container', 'container')

@section('content')
    @include('organizations.single', compact('organization'))
@endsection