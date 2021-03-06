@extends('app')

@section('title', 'What\'s on your mind? | Wichitawesome!')
@section('description', 'Send us some feedback.')

@section('container', 'container')

@section('content')
    <h2 class="mt0">You can email us, we're like BFFs now.</h2>
    <p>We put a lot of hours into making this little slice of the interwebs the best it can be. Let us know if we can make something better. If we're doing something right, you could always send us a poem.</p>
    <form method="post" action="{{ action('FeedbackController@send') }}">
        <div class="form-head">
            @include('errors.form')
        </div>
        <div class="form-body sm-mxn1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="rel mb2 sm-col sm-col-6 sm-px1">
                <label class="caps h5 abs" for="name">Name</label>
                <input type="text" class="blk col-12 mb1 field" id="name" name="name" value="{{ old('name') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-6 sm-px1">
                <label class="caps h5 abs" for="email">Email</label>
                <input type="email" class="blk col-12 mb1 field" id="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <label class="caps h5 abs" for="description">Message</label>
                <textarea class="blk col-12 mb1 field" id="description" name="description" rows="1">{{ old('description') }}</textarea>
            </div>

            <div class="rel mb2 sm-col sm-col-12 sm-px1">
                <button type="submit" class="btn caps blk col-12">Send</button>
            </div>
        </div>
    </form>
@endsection