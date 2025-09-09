@extends('admin.layouts.master')
@section('title')
    Create a route
@endsection
@section('content')

    <div class="content-wrapper">
        <div class="col-md-8 col-lg-3 col-sm-12">
            <form action="{{ !empty($routelist) ? route('routelist_update') : route('routelist_store') }}" method="post">
                @csrf
                @if (!empty($routelist))
                    <input type="hidden" name="id" value="{{ $routelist->id }}">
                @endif
                <div class="form-content">

                    <div class="form-group name">
                        <label for="route_title">Route Title: </label>
                        <input type="text" class="form-control" placeholder="Enter route title" name="route_title"
                            value="{{ !empty($routelist) ? $routelist->route_title : old('route_title') }}" required>
                    </div>

                    <div class="form-group email">
                        <label for="route_name">Route Name: </label>
                        <input type="text" class="form-control" placeholder="Enter route name" name="route_name"
                            value="{{ !empty($routelist) ? $routelist->route_name : old('route_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Route Group: </label>
                        @php 
                            $routeGroup = $Query::getData('route_groups');
                        @endphp
                         <select class="form-select" name="route_group">
                            <option value="">Select one</option>
                            @foreach($routeGroup as $index => $group)
                            <option value={{$group->id}}
                                {{ !empty($routelist) && $routelist->route_group == $group->id ? 'selected' : '' }}>{{$group->name}}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Route Description: </label>
                        <textarea required class="form-control"
                            name="route_description">{{ !empty($routelist) ? $routelist->route_description : old('route_description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Route Icon: </label>
                        <input type="text" class="form-control" placeholder="Route icon" name="route_icon"
                            value="{{ !empty($routelist) ? $routelist->route_icon : old('route_icon') }}">
                    </div>

                    <div class="form-group">
                        <label>Route Order: </label>
                        <input type="text" class="form-control" placeholder="Route Order" name="route_order"
                            value="{{ !empty($routelist) ? $routelist->route_order : old('route_order') }}">
                    </div>

                    <div class="form-group select arrow_class">
                        <label for="select">Show in menu </label>
                        <select class="form-select select-box" name="show_menu" aria-label=".form-select-lg" id="show_menu">
                            <option value="">Select one</option>
                            <option value="Yes"
                                {{ !empty($routelist) && $routelist->show_menu == 'Yes' ? 'selected' : '' }}>Yes
                            </option>
                            <option value="No"
                                {{ !empty($routelist) && $routelist->show_menu == 'No' ? 'selected' : '' }}>No
                            </option>
                        </select>
                    </div>


                    <!-- dashboard Menu position -->
                    <div class="form-group">
                        <label for="">Dashboard menu position</label>
                            <div class="form-check">
                                @php
                                    $menuPosition = ['Left', 'Right', 'Top', 'Bottom'];
                                @endphp
                                @foreach($menuPosition as  $value)
                                <div class="form-group d-inline-flex me-2">
                                    <input type="checkbox" id="routelist_index_{{$value}}" class="checkItem" name="dashboard_position[]" value="{{$value}}" {{!empty($routelist) && strstr($routelist->dashboard_position, $value) ? 'checked' : ''}}>
                                    <label class="w-100" for="routelist_index_{{$value}}">{{$value}}</label>
                                </div>
                                @endforeach
                            </div>
                        
                    </div>
                    <!-- End Dahboard Menu Position -->

                    <div class="form-submit_btn">
                        <button type="submit" class="btn blue">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
