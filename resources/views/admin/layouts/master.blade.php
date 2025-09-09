<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!--Meta Tag-->
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--website title-->
    <title>
        @yield('title')
    </title>

    <!--====favicon icon====-->
    {{--    <link rel=icon href="images/logo.png" type="image/png" sizes=16x16>--}}
    @include('admin.layouts.css')
    @yield('cuscss')
</head>

<body>
<noscript> <META HTTP-EQUIV="Refresh" CONTENT="0;URL={{route('502')}}?message='Please Enable Javascript'"> </noscript>
    <div id="app">
        <!-- header wrapper-->
        @include('admin.layouts.header')

        <!--full content wrapper -->
        <section class="full-content-wrapper">
            <div class="cnt-left-site_info mobile-view">
                <div id="mySidenav" class="sidenav" style="display: none;">
                    <a href="javascript:void(0)" class="closebtn" id="closebtn" onclick="closeNav()">&times;</a>
                    <div class="container-fluid">

                        <div class="sub-category-info">
                            @include('admin.layouts.nav-menu.left-sidebar-menu')
                            <div class="summery_list">
                                @yield('summary')
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar_open">
                    <span onclick="openNav()"><i class="fas fa-chevron-right"></i></span>
                </div>
            </div>
            <div class="cnt-left-site_info desktop-view" style="z-index: 2; overflow: auto">
                <div class="container-fluid">
                    <div class="sub-category-info">
                        @include('admin.layouts.nav-menu.left-sidebar-menu')
                        <div class="summery_list">
                            @yield('summary')
                        </div>
                    </div>
                </div>
            </div>
            <div class="cnt-right-site_info">
                <!-- Page Title /Name /Filter --->
                <div class="master-title-wrapper">
                    @hasSection('onlytitle')
                        <div class="row">
                            <div class="col-md-12">
                                @yield('onlytitle')
                            </div>
                        </div>
                    @else
                        @hasSection('title')
                        <div class="cnt-right-top_header">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-2">
                                        <div class="recent-info d-flex">
                                            @yield('title')
                                        </div>
                                    </div>
                                    <div class="col-lg-10 col-sm-12 text-right">
                                        @hasSection('filterleft')
                                            <div class="category-related-link recent-info" style="justify-content: flex-start">
                                                @yield('filterleft')
                                            </div>
                                        @endif
                                        <div class="category-related-link">
                                            @yield('filter')
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endif
                    @endif
                </div>
                <!-- Page Description -->
                <div class="positive-relative">
                    @if(!empty(request()->get('hasPermission')))
                        @yield('content')
                        <div style="clear: both; "></div>
                        {{-- <div class="breadcrumb-content">
                            <div class="breadcrumb">
                                @yield('breadcrumb')
                            </div>
                        </div> --}}
                        <div style="clear: both; "></div>
                    @else
                        <div class="content-wrapper">
                            <div class="alert alert-warning">
                                You have no permission for this route.
                            </div>
                        </div>
                    @endif
                    <!-- footer wrapper-->
                    @include('admin.layouts.footer')
                </div>
            </div>
        </section>

    </div>
    @include('admin.layouts.js')
    @include('admin.layouts.notification')
    @yield('cusjs')
    @yield('bottomjs')
</body>

</html>
