@extends('app')

@section('title', 'Users')
@section('description', '')

@section('container', '')

@section('content')
    <div class="mb2">
        <a class="btn caps bg-green" href="{{ action('UserController@create') }}">New User</a>
    </div>
    <div class="col-12 o-auto h5">
        <table>
            <thead>
                <tr>
                    <th scope="col" class="h6">#<span class="sr-only"> ID</span></th>
                    <th scope="col" class="h6">Name</th>
                    <th scope="col" class="h6">Email</th>
                    <th scope="col" class="h6">Role</th>
                    <th scope="col" class="h6">Date</th>
                    <th scope="col" class="h6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><a href="{{ action('UserController@edit', $user->id) }}">{{ $user->name }}</a></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->name }}</td>
                        <td>{{ $user->created_at->format('m/d/Y') }}</td>
                        <td class="nowrap center tbl-cell">
                            <form method="post" action="{{  action('UserController@destroy',$user->id) }}" onsubmit="return confirm('You definitely want to delete this user?');">
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