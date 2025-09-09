@extends('admin.layouts.master')

@section('title')
    Manage Warehouse
@endsection

@section('content')

    <div class="content-wrapper gird-box">
        <div class="container-fluid">
            <div class="row">
                @foreach ($wh as $data)
                    <div class="col-sm-6 col-md-4 col-lg-3 my-2">
                        <div class="gird-box_list">
                            <div class="xaction float-end">
                                {!! $ButtonSet::delete('warehouse_destroy', $data->id) !!}
                                {!! $ButtonSet::edit('warehouse_edit', $data->id) !!}
                                {!! $ButtonSet::view('warehouse_single_index', $data->code) !!}
                                @php
                                    $crole = auth()->user()->getUserRole();
                                    $r = auth()->user()->checkRoute($crole, 'warehouse_edit');
                                    //dump($r);
                                    //dump($crole);
                                @endphp
                            </div>
                            <h2>
                                @if(auth()->user()->hasRoutePermission('warehouse_single_index'))
                                    <a class="text-primary" href="{{route('warehouse_single_index', $data->code)}}"> {{ $data->name }}</a>
                                @else
                                {{ $data->name }}
                                @endif
                            </h2>
                            <h4 class="title">
                                <strong>ID:</strong> {{ $data->id }}
                            </h4>
                            <h4 class="title">
                                <strong>Location:</strong> {{ $data->location }}
                            </h4>
                            <h4 class="title">
                                <strong>Phone:</strong> {{ $data->phone }}
                            </h4>
                            <h4 class="title">
                                <strong>Email:</strong> {{ $data->email }}
                            </h4>
                            <h4 class="title">
                                @if($data->user_id == auth()->user()->id)
                                    As a {{$Query::accessModel('Role')::name($data->role_id)}}
                                @endif
                            </h4>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


@endsection
