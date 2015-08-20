@extends('app')

@section('title', 'Roles')
@section('description', '')

@section('container', '')

@section('content')
    <div class="mb2">
        <a class="btn caps bg-green" href="{{ action('RoleController@create') }}">New role</a>
    </div>
    <div class="col-12 o-auto h5">
        <table>
            <thead>
                <tr>
                    <th>#<span class="sr-only"> ID</span></th>
                    <th>Name</th>
                    <th>Users</th>
                    <th>Permissions</th>
                    <th><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td><a href="{{ action('RoleController@edit', $role->id) }}">{{ $role->name }}</a></td>
                        <td>{{ count($role->users) }}</td>
                        <td>{{ count($role->permissions) }}</td>
                        <td>
                            <form method="post" action="{{  action('RoleController@destroy',$role->id) }}" onsubmit="return confirm('You definitely want to delete this role?');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" class="btn bg-dark-red p1"><span class="i i-delete h2"></span></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection