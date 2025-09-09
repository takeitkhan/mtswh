@php
    $topLeftMenu = $NavMenu::showMenu('Top');
@endphp


<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav">
{{--        <li class="nav-item">--}}
{{--            <a class="nav-link " aria-current="page">MTS Warehouse</a>--}}
{{--        </li>--}}
        @foreach ($topLeftMenu as  $menus)

            @php $routes = $menus['routes'] @endphp

            {{-- if Have Group This Menu || Sub Menu--}}
                @if(count($routes) > 0 && !empty($menus['index']))
                <li class="nav-item menu-item">
                    <a class="nav-link dropdown-toggle p{{ $menus['index']}}"
                        href="javascript:void(0)" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ $menus['index']}}
                    </a>
                    <ul class="sub-menu dropdown-menu">
                        @foreach($routes as $user)
                        <li>
                            @php
                                $href= route($user['route_name'], $user['data_id'] ?? null).$user['any_get_method'];
                            @endphp
                             {{-- Add Parent Index Color css  --}}
                            @if(Request::url() == $href)
                                <style>
                                .p<?php echo $menus['index'];?>{
                                    color: #0d6efd !important;
                                }
                                </style>
                            @endif
                           {{-- k --}}
                            <a class="{{Request::url() == $href ? 'text-primary' : ''}}"
                                href="{{ route($user['route_name'], $user['data_id'] ?? null) }}">
                                {{ $user['route_title'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @else
                 {{-- if Single Menu--}}
                    @foreach($routes as $user)
                    @php
                        $href= route($user['route_name'], $user['data_id'] ?? null).$user['any_get_method'];
                    @endphp
                    <li class="nav-item menu-item">
                        <a class="nav-link {{Request::url() == $href ? 'text-primary' : ''}}"
                        href="{{ route($user['route_name'], $user['data_id'] ?? null) }}">
                            {{ $user['route_title'] }}
                        </a>
                    </li>
                    @endforeach
                @endif

            {{-- Single Menu --}}
        @endforeach
    </ul>
</div>
