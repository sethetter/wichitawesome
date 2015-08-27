@extends('app')

@section('title', 'Organizations')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('OrganizationController@store') }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body sm-mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="button" class="btn right bg-light-gray" id="btn_facebook">Pull Facebook Info</button>
                <div class="o-hidden">
                    <label for="fb_url" class="caps h5 abs">Facebook URL</label>
                    <input type="url" data-url="fb-page" class="blk field col-12" id="fb_url" name="fb_url" value="{{ old('fb_url', $fb_url) }}">
                    <input type="hidden" id="facebook" name="facebook" value="{{ old('facebook') }}">
                </div>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="name" class="caps h5 abs">Name</label>
                <input type="text" class="blk field col-12" id="name" name="name" value="{{ old('name') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="twitter" class="caps h5 abs">Twitter Handle</label>
                <input type="text" class="blk field col-12" id="twitter" name="twitter" value="{{ old('twitter') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="website" class="caps h5 abs">Website</label>
                <input type="url" class="blk field col-12" id="website" name="website" value="{{ old('website') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="email" class="caps h5 abs">Email</label>
                <input type="email" class="blk field col-12" id="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="phone" class="caps h5 abs">Phone</label>
                <input type="tel" class="blk field col-12" id="phone" name="phone" value="{{ old('phone') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label for="description" class="caps h5 abs">Description</label>
                <textarea id="description" class="blk field col-12" name="description" rows="1">{{ old('description') }}</textarea>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="submit"  class="btn blk col-12">Create</button>
            </div>
        </div>
    </form>
@endsection