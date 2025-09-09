@extends('layouts.app')

@section('content')
<?php
                        // /dd(auth()->user()->routeList(request()->get('authGeneralRole')));   
                        //dd(request()); 
                    ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Menu') }}</div>

                <div class="card-body">
                    
                    @foreach(auth()->user()->routeList(request()->get('authGeneralRole')) as $key => $value)
                        <ul>
                            <li>
                                <a href="{{route($value->route_name)}}">{{$value->route_title}}</a>
                            </li>
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection