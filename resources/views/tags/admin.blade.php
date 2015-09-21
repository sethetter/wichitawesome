@extends('app')

@section('title', 'Tags')
@section('description', '')

@section('content')
    <div class="px2 py4 clearfix">
        <div class="mb2">
            <a class="btn caps bg-green" href="{{ action('TagController@create') }}">New tag</a>
        </div>
        <div class="col-12 o-auto h5">
            <table>
                <thead>
                    <tr>
                        <th scope="col" class="h6">#<span class="sr-only"> ID</span></th>
                        <th scope="col" class="h6">Name</th>
                        <th scope="col" class="h6">Slug</th>
                        <th scope="col" class="h6 center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td><a href="{{ action('TagController@edit', $tag->id) }}">{{ $tag->name }}</a></td>
                            <td>{{ $tag->slug }}</td>
                            <td class="nowrap center tbl-cell">
                                <form method="post" action="{{  action('TagController@destroy',$tag->id) }}" onsubmit="return confirm('You definitely want to delete this tag?');">
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