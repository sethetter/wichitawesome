@extends('app')

@section('title', 'Organizations')
@section('description', '')

@section('content')
    <div class="px2 py4 clearfix">
        <div class="mb2">
            <a class="btn caps bg-green" href="{{ action('OrganizationController@create') }}">New Organization</a>
        </div>
        <div class="col-12 o-auto h5">
            <table>
                <thead>
                    <tr>
                        <th scope="col" class="h6">#<span class="sr-only"> ID</span></th>
                        <th scope="col" class="h6">Name</th>
                        <th scope="col" class="h6 center">Facebook</th>
                        <th scope="col" class="h6">Twitter</th>
                        <th scope="col" class="h6">Website</th>
                        <th scope="col" class="h6 center">Email</th>
                        <th scope="col" class="h6 center">Phone</th>
                        <th scope="col" class="h6 center">Visibiliy</th>
                        <th scope="col" class="h6 center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($organizations as $organization)
                        <tr>
                            <td>{{ $organization->id }}</td>
                            <td><a href="{{ action('OrganizationController@edit', $organization->id) }}">{{ $organization->name }}</a></td>
                            <td class="center tbl-cell">
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
                            <td class="center tbl-cell">
                                @if($organization->email)
                                    <a target="_blank" href="mailto:{{ $organization->email }}"><svg class="i"><use xlink:href="#icon-mail"></use></svg></a>
                                @endif
                            </td>
                            <td class="center tbl-cell">
                                @if($organization->phone)
                                    <a target="_blank" href="tel:{{ $organization->phone }}"><svg class="i"><use xlink:href="#icon-smartphone"></use></svg></a>
                                @endif
                            </td>
                            <td class="center tbl-cell">
                                @if($organization->visible)
                                    <a target="_blank" href="{{ action('OrganizationController@show',$organization->id) }}"><svg class="green i"><use xlink:href="#icon-visibility"></use></svg></a>
                                @else
                                    <svg class="red i"><use xlink:href="#icon-visibility-off"></use></svg>
                                @endif
                            </td>
                            <td class="nowrap center tbl-cell">
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
    </div>
@endsection