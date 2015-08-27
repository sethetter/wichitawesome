@extends('app')

@section('title', 'Organizations')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('OrganizationController@update', $organization->id) }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="put">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="name">Name</label>
                <input type="text" class="blk col-12 mb1 field" id="name" name="name" value="{{ old('name', $organization->name) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="facebook">Facebook ID</label>
                <input type="number" class="blk col-12 mb1 field" id="facebook" name="facebook" value="{{ old('facebook', $organization->facebook) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="twitter">Twitter Handle</label>
                <input type="text" class="blk col-12 mb1 field" id="twitter" name="twitter" value="{{ old('twitter', $organization->twitter) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="website">Website</label>
                <input type="url" class="blk col-12 mb1 field" id="website" name="website" value="{{ old('website', $organization->website) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="email">Email</label>
                <input type="email" class="blk col-12 mb1 field" id="email" name="email" value="{{ old('email', $organization->email) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="phone">Phone</label>
                <input type="tel" class="blk col-12 mb1 field" id="phone" name="phone" value="{{ old('phone', $organization->phone) }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="description">Description</label>
                <textarea class="blk col-12 mb1 field" id="description" name="description">{{ old('description', $organization->description) }}</textarea>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label><input type="radio" id="visible_true" name="visible" value="1" {{ (old('visible', $organization->visible) == 1) ? 'checked' : '' }}>Show</label>
                <label><input type="radio" id="visible_false" name="visible" value="0" {{ (old('visible', $organization->visible) == 0) ? 'checked' : '' }}>Hide</label>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="submit" class="btn blk col-12">Update</button>
            </div>
        </div>
    </form>
@endsection