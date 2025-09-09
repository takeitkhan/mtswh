@extends('admin.layouts.master')


@section('name', 'Test View')

@section('content')
    <div class="content-wrapper">
        @php
            $userInfo = auth()
                ->user()
                ->userInfo();
            //print_r(array_column($userInfo->roles->toArray(), 'role_id'));
            // dump(
            //     auth()
            //         ->user()
            //         ->getUserWarehouse(),
            // );
            //dump($userInfo);
            //dd(auth()->user()->getUserWarehouse());
            //dd(auth()->user()->checkUserRoleTypeGlobal());
            //dd(auth()->user()->getUserRouteList());
        @endphp

        <p>Name: {{ auth()->user()->name }}</p>

        <h4> Role of this user</h4>
        <ul>
            @foreach (auth()->user()->getUserRoleDetails()
        as $role)
                <li>{{ $role->name }}</li>
            @endforeach
        </ul>

        <br>

        <h4> Routelist of this user</h4>
            @foreach (auth()->user()->getUserRouteList() as  $index => $routes)
            <h5>{{$index}}</h5>
            <ul>
                @foreach($routes as $user)
                    @if($user->show_menu == 'Yes' && $user->dashboard_position == 'Left')
                        <li>{{ $user->route_title }} - {{ $user->route_name }}</li>
                    @endif
                @endforeach
            </ul>
            @endforeach
        
        <br>
        <h4> Warehouse of this user</h4>
        <ul>
            @foreach (auth()->user()->getUserWarehouse()
        as $wh)
                <li>{{ $wh->name }} - {{$Query::accessModel('Role')::name($wh->role_id)}}</li>
            @endforeach
        </ul>

    </div>

@endsection
