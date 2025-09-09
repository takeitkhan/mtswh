@php
/**
 * request()->get('warehouse_code')
 * its registered from
 * SingleWarehouseController
 * =========================
 * 1st parameter = 'Warehouse'
 * This define in to route/web.php
 * this use Show_for
 * Route group Property
 * */


if(request()->get('warehouse_code')){
    $homeLeftMenu = $NavMenu::showMenu('Left','Warehouse', request()->get('warehouse_code'));
}else{

}
$homeLeftMenuGlobal = $NavMenu::showMenu('Left');
$sideMenu = [
    $homeLeftMenuGlobal,
    $homeLeftMenu ?? [],
];
@endphp
@foreach($sideMenu as $homeLeftMenu)
    @foreach ($homeLeftMenu as  $menus)
        @if($menus['routes'])
            <div class="sub-category-info_links">
                <div class="leftsidebarmenu-title-wrapper">
                    <div class="cnt-right-top_header">
                        @php $routes = $menus['routes'] @endphp
                        {{count($routes) > 0 ? $menus['index'] : ''}}
                    </div>
                </div>
                <ul>
                    @foreach($routes as $user)
                            <li>
                                @php
                                    $href= route($user['route_name'], $user['data_id'] ?? null).$user['any_get_method'];
                                @endphp
                                <a
                                    href="{{ $href }}"
                                    class="{{Request::url() == $href ? 'text-primary' : ''}}">
                                    <i class="{{ $user['route_icon'] }}"></i> {{ $user['route_title'] }}
                                </a>
                            </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach
@endforeach
