@extends('app')

@section('title', 'Users')
@section('description', '')

@section('container', 'container')

@section('content')
    <form method="post" action="{{ action('UserController@update', $user->id) }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="put">

            <div class="form-group">
                <label class="caps h5 abs" for="name">Name</label>
                <input type="text" class="blk col-12 mb1 field" id="name" name="name" value="{{ $user->name }}">
            </div>

            <div class="form-group">
                <label class="caps h5 abs" for="email">Email</label>
                <input type="email" class="blk col-12 mb1 field" id="email" name="email" value="{{ $user->email }}">
            </div>

            <div class="form-group">
                <label class="caps h6" for="role_id">Role</label>
                <select class="blk col-12 mb1 field" id="role_id" name="role_id">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ ($user->role_id == $role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="caps h5 abs" for="password">Password</label>
                <input type="password" class="blk col-12 mb1 field" id="password" name="password">
            </div>

            <div class="form-group">
                <label class="caps h5 abs" for="password_confirmation">Confirm Password</label>
                <input type="password" class="blk col-12 mb1 field" name="password_confirmation">
            </div>

            <div class="form-group">
                <button type="submit" class="btn blk col-12">Update</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('js/form.js') }}"></script>
@endsection