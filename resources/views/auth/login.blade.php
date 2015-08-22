@extends('app')

@section('title', 'Login')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ url('auth/login') }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body sm-mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="email">Email</label>
                <input type="email" class="blk field col-12" id="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="password">Password</label>
                <input type="password" class="blk field col-12" id="password" name="password">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5"><input type="checkbox" name="remember"> Remember Me</label>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="submit" class="btn blk col-12">Login</button>
            </div>
        </div>
    </form>
@endsection