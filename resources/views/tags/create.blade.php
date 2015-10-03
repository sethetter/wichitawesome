@extends('app')

@section('title', 'Tags')
@section('description', '')

@section('content')
    <div class="px2 py4 container clearfix">
        <form method="post" action="{{ action('TagController@store') }}">
            <div class="form-head">
                @include('errors.form')
            </div>
            <div class="form-body sm-mxn1">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label class="caps h5 abs" for="name">Name</label>
                    <input type="text" class="blk col-12 rel z1 field" id="name" name="name" value="{{ old('name') }}">
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <button type="submit" class="btn blk col-12">Save</button>
                </div>
            </div>
        </form>
    </div>
@endsection