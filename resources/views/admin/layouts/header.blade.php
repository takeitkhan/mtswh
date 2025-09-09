<header class="header-wrapper">
    <nav class="navbar fixed-top navbar-expand-lg navbar-light py-0">
        <div class="container-fluid">
            <div class="menu-item">
                <a class="navbar-brand font-15 fw-bold" href="javascript:void(0)">
                    {{--                    <i class="fa-solid fa-3"></i> --}}

                    <span class="d-lg-none" onclick="openNav()"><i class="fas fa-th"></i></span>

                    @if(request()->get('warehouse_name'))
                        <span class="fw-bold text-primary">{{request()->get('warehouse_name')}}</span>
                    @else
                        MTS Warehouse
                    @endif

                </a>
                <ul class="sub-menu" id="toggleClass">
                    <li>
                        <a type="button" onclick="createCustomAlert('Developed By Tritiyo Limited')">About Software</a>
                    </li>
                    <?php /*
                    <li>
                        <a href="javascript:void(0)">menu 1</a>
                        <i class="fas fa-caret-right"></i>
                        <ul class="sub-menu ">
                            <li>
                                <a href="javascript:void(0)">
                                    menu 4
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    menu 5
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0)">menu 1</a>
                    </li>
 */ ?>
                </ul>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span>
                    <i class="fas fa-bars"></i>
                </span>
            </button>

            <!-- menu list -->
        @include('admin.layouts.nav-menu.top-left-menu')
        <!-- header right site -->
            <div class="header-right_site">
                <div class="header-spl-icon">
                    <ul>
                        @if(auth()->user()->checkUserRoleTypeGlobal())
                            <li>
                                <a href="{{route('upload_routelist')}}" title="Reload Routelist">
                                    <i class="fas fa-sync"></i>
                                </a>
                            </li>
                        @endif
                        @php
                            $countNotification = $Model('PpiSpiStatus')::notifications(['count' => true, 'is_read' => true]);
                            $ncs =  $Model('PpiSpiStatus')::notifications(['paginate' => 3, 'is_read' => true]);
                        @endphp
                        @if(!empty($ncs))
                            <li id="notification_li">
                                <a href="#" id="notificationLink">
                                    <i class="fa fa-bell"></i>

                                    @if($countNotification > 0)
                                        <span id="notification_count">
                                {!! $countNotification  !!}
                            </span>
                                    @endif
                                </a>
                                <div id="notificationContainer">
                                    <div id="notificationTitle">Notifications</div>
                                    <div id="notificationsBody" class="notifications">
                                        <ul class="list-group">

                                            @if($ncs->isEmpty())
                                                <li class="text-center py-3 font-12">There are no notifications</li>
                                            @else
                                                @foreach($ncs as $data)
                                                    <li class="list-group-item">
                                                        <p class="font-13">
                                                            <a class="text-primary"
                                                               href="">{{$data->message}} </a>
                                                        </p>
                                                        <p class="font-11 text-secondary">
                                                            {{$data->created_at->format('Y-m-d h:i a')}}
                                                            . {{$Model('Warehouse')::name($data->warehouse_id)}}
                                                        </p>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                    <div id="notificationFooter"><a href="{{route('admin_dashboard')}}">See All</a></div>
                                </div>
                            </li>
                        @endif

                        <li>
                            <a id="btnFullScreen" data-full_screen="exit" xonclick="browserFullScreen()"
                               href="javascript:void(0)" role="button">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </a>
                        </li>
                        <?php /*
                        <li>
                            <a href="javascript:void(0)">
                                <i class="fas fa-wifi"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)">
                                <i class="fas fa-search"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)">
                                <i class="fas fa-battery-full"></i>
                            </a>
                        </li>
                        */ ?>
                        <li class="menu-item">
                            <a href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="far fa-user"></i>
                            </a>
                            <ul class="sub-menu dropdown-menu">
                                <li>
                                    <a href="{{route('user_edit_profile', auth()->user()->id)}}">Edit Profile</a>
                                </li>

                            </ul>
                        </li>
                        <li class="date_info">
                            <span>
                               <a href="{{route('user_edit_profile', auth()->user()->id)}}"> {{auth()->user()->name}}</a>
                            </span>
                        </li>
                        <li class="date_info">
                            <span>
                                <a class="nav-link" id="userLogout" xhref="{{ route('logout') }}" title="Logout"
                                   type="button"
                                   xonclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    <i class="fa fa-power-off"></i>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>

{!! $Component::jsModal('userLogout', ['formAction' => route('logout'), 'modalHeader' => 'Are you confirm to logout']) !!}
