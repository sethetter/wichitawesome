@extends('app')

@section('title', 'Permissions')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('PermissionController@update', $permission->id) }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body sm-mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="put">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="name">Name</label>
                <input type="text" class="block col-12 mb1 field" id="name" name="name" value="{{ $permission->name }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="submit" class="btn blk col-12">Update</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('js/form.js') }}"></script>
@endsection