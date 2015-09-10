@extends('app')

@section('title', 'Organizations')
@section('description', '')

@section('container', '')

@section('content')
    <div class="mb2">
        <a class="btn caps bg-green" href="{{ action('OrganizationController@create') }}">New Organization</a>
    </div>
    <div class="col-12 o-auto h5">
        <table>
            <thead>
                <tr>
                    <th>#<span class="sr-only"> ID</span></th>
                    <th>Name</th>
                    <th>Facebook</th>
                    <th>Twitter</th>
                    <th>Website</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Visibiliy</th>
                    <th><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($organizations as $organization)
                    <tr>
                        <td>{{ $organization->id }}</td>
                        <td><a href="{{ action('OrganizationController@edit', $organization->id) }}">{{ $organization->name }}</a></td>
                        <td class="center">
                            @if($organization->facebook)
                                <a target="_blank" href="https://facebook.com/{{ $organization->facebook }}"><svg class="i"><use xlink:href="#icon-launch"></use></svg></a>
                            @endif
                        </td>
                        <td>
                            @if($organization->twitter)
                                <a target="_blank" href="https://twitter.com/{{ $organization->twitter }}">{{ '@'.$organization->twitter }}</a>
                            @endif
                        </td>
                        <td>
                            @if($organization->website)
                                <a target="_blank" href="{{ $organization->website }}">{{ $organization->website }}</a>
                            @endif
                        </td>
                        <td class="center">
                            @if($organization->email)
                                <a target="_blank" href="mailto:{{ $organization->email }}"><svg class="i"><use xlink:href="#icon-mail"></use></svg></a>
                            @endif
                        </td>
                        <td class="nowrap">
                            @if($organization->phone)
                                <a target="_blank" href="tel:{{ $organization->phone }}">{{ $organization->phone }}</a>
                            @endif
                        </td>
                        <td class="center">
                            @if($organization->visible)
                                <a target="_blank" href="{{ action('OrganizationController@show',$organization->id) }}"><svg class="green i"><use xlink:href="#icon-visibility"></use></svg></a>
                            @else
                                <svg class="red i"><use xlink:href="#icon-visibility-off"></use></svg>
                            @endif
                        </td>
                        <td class="nowrap">
                            <form class="inl-blk" method="post" action="{{  action('OrganizationController@destroy',$organization->id) }}" onsubmit="return confirm('You definitely want to delete this organization?');">
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