@extends('admin.layouts.master')
@section('content')
    <div class="alert alert-warning">
        @if(request()->get('message'))
            {!! request()->get('message') !!}
        @else
            Page not found
        @endif
    </div>
@endsection
