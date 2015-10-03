@extends('app')

@section('title', 'Roles')
@section('description', '')

@section('content')
    <div class="px2 py4 container clearfix">
        <form method="post" action="{{ action('RoleController@update', $role->id) }}">
            <div class="form-head">
                @include('errors.form')
            </div>
            <div class="form-body sm-mxn1">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="put">

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label class="caps h5" for="name">Name</label>
                    <input type="text" class="blk col-12 rel z1 field" id="name" name="name" value="{{ $role->name }}">
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <label class="caps h5" for="permission_id">Role</label>
                    <div class="clearfix mxn1">
                        @foreach($permissions as $permission)
                            <label class="sm-col sm-col-6 px1">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ (in_array($permission->id, old('permissions', $role->permissions->lists('id')->toArray()))) ? 'checked' : '' }}> {{ $permission->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="rel mb2 sm-col sm-col-12 sm-px1">
                    <button type="submit" class="btn blk col-12">Update</button>
                </div>
            </div>
        </form>
    </div>
@endsection