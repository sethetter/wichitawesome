@extends('app')

@section('title', 'Events')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('EventController@store') }}">
        <div class="form-head">
            @include('errors.form')
        </div>
       <div class="form-body mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="rel mb2 col col-12 px1">
                <label class="caps h5 abs" for="name">Name</label>
                <input type="text" class="blk col-12 mb1 field" id="name" name="name" value="{{ old('name') }}">
            </div>

            <div class="rel mb2 col col-6 sm-col-3 px1">
                <label class="caps h5 abs" for="s_date">Start Date</label>
                <input type="text" class="blk col-12 mb1 field date-input" id="s_date" name="s_date" value="{{ old('s_date') }}">
            </div>

            <div class="rel mb2 col col-6 sm-col-3 px1">
                <label class="caps h5 abs" for="s_time">Start Time</label>
                <input type="text" class="blk col-12 mb1 field time-input" id="s_time" name="s_time" value="{{ old('s_time') }}">
            </div>

            <div class="rel mb2 col col-6 sm-col-3 px1">
                <label class="caps h5 abs" for="e_date">End Date</label>
                <input type="text" class="blk col-12 mb1 field date-input" id="e_date" name="e_date" value="{{ old('e_date') }}">
            </div>

            <div class="rel mb2 col col-6 sm-col-3 px1">
                <label class="caps h5 abs" for="e_time">End Time</label>
                <input type="text" class="blk col-12 mb1 field time-input" id="e_time" name="e_time" value="{{ old('e_time') }}">
            </div>

            <div class="rel mb2 col col-12 px1">
                <label for="venue_name" class="caps h5 abs">Location</label>
                <input type="text" class="field col-12" id="venue_name" name="venue[name]" value="{{ old('venue.name') }}">
            </div>

            <div class="rel mb2 col col-12 px1">
                <label class="caps h6" for="venue_id">Venue</label>
                <select class="blk col-12 mb1 field" id="venue_id" name="venue_id">
                    @foreach($venues as $venue)
                        <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="rel mb2 col col-12 px1">
                <label class="caps h5 abs" for="facebook">Facebook</label>
                <input type="number" class="blk col-12 mb1 field" id="facebook" name="facebook" value="{{ old('facebook') }}">
            </div>

            <div class="rel mb2 col col-12 px1">
                <label class="caps h5 abs" for="hashtag">Hashtag</label>
                <input type="text" class="blk col-12 mb1 field" id="hashtag" name="hashtag" value="{{ old('hashtag') }}">
            </div>

            <div class="rel mb2 col col-12 px1">
                <label class="caps h5 abs" for="description">Description</label>
                <textarea class="blk col-12 mb1 field" id="description" name="description">{{ old('description') }}</textarea>
            </div>

            <div class="rel mb2 col col-12 px1">
                <button type="submit" class="btn blk col-12">Update</button>
            </div>
        </div>
    </form>
@endsection