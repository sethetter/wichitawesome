@extends('app')

@section('title', 'Organizations')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('OrganizationController@store') }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="button" class="btn right bg-light-gray" id="btn_facebook">Pull Facebook Info</button>
                <div class="o-hidden">
                    <label class="caps h5 abs" for="fb_url">Facebook URL</label>
                    <input type="url" data-url="fb-page" class="blk col-12 mb1 field" id="fb_url" name="fb_url" value="{{ old('fb_url') }}">
                    <input type="hidden" id="facebook" name="facebook" value="{{ old('facebook') }}">
                </div>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="name">Name</label>
                <input type="text" class="blk col-12 mb1 field" id="name" name="name" value="{{ old('name') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="twitter">Twitter Handle</label>
                <input type="text" class="blk col-12 mb1 field" id="twitter" name="twitter" value="{{ old('twitter') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="website">Website</label>
                <input type="url" class="blk col-12 mb1 field" id="website" name="website" value="{{ old('website') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="email">Email</label>
                <input type="email" class="blk col-12 mb1 field" id="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="phone">Phone</label>
                <input type="tel" class="blk col-12 mb1 field" id="phone" name="phone" value="{{ old('phone') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="description">Description</label>
                <textarea class="blk col-12 mb1 field" id="description" name="description" rows="1">{{ old('description') }}</textarea>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="submit" class="btn blk col-12">Create</button>
            </div>
        </div>
    </form>
@endsection