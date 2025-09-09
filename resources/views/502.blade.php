@extends('layouts.app')

@section('content')
    <div class="container justify-content-center">
        <div class="alert alert-warning">
            @if(request()->get('message'))
                {!! request()->get('message') !!}
            @else
                Page not found
            @endif
        </div>
    </div>

@endsection
