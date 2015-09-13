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
                    <th scope="col" class="h6">#<span class="sr-only"> ID</span></th>
                    <th scope="col" class="h6">Name</th>
                    <th scope="col" class="h6">Users</th>
                    <th scope="col" class="h6">Permissions</th>
                    <th scope="col" class="h6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td><a href="{{ action('RoleController@edit', $role->id) }}">{{ $role->name }}</a></td>
                        <td>{{ count($role->users) }}</td>
                        <td>{{ count($role->permissions) }}</td>
                        <td class="nowrap center tbl-cell">
                            <form method="post" action="{{  action('RoleController@destroy',$role->id) }}" onsubmit="return confirm('You definitely want to delete this role?');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="delete" />
                                <button type="submit" class="dark-red" style="padding:0;background:none;"><svg class="i"><use xlink:href="#icon-bomb"></use></svg></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection