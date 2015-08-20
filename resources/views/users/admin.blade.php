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
                    <th>#<span class="sr-only"> ID</span></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date</th>
                    <th><span class="sr-only">Actions</span></th>
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
                        <td>
                            <form method="post" action="{{  action('UserController@destroy',$user->id) }}" onsubmit="return confirm('You definitely want to delete this user?');">
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