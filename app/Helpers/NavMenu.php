<?php 
namespace App\Helpers;
use App\Models\Routelist;
use App\Models\Routegroup;
use Illuminate\Support\Facades\Route;
class NavMenu {

    public static function getAuthUserRole(){
        //its registered from Middelware WarehousePemission 
        return request()->get('authUserRole');
    }


    //Home Left / Top / Bottom Side Menu
    public static function showMenu($display_position, $show_for = null, $data_id = null, $any_get_mehod= null){
        $makeMenu = [];

        /**
         * if need
         * get All Route of  Current User All Role
         *  auth()->user()->getUserRouteList();
         */
         
        foreach (auth()->user()->routelist(self::getAuthUserRole()) as  $index => $routes){
            $n = [];
            foreach($routes as $user){
                if($user->show_menu == 'Yes' && strstr($user->dashboard_position , $display_position) == true && $user->show_for == $show_for){
                    if(Route::has($user->route_name)){
                        $n []= [
                            'route_name' =>  $user->route_name,
                            'route_icon' => $user->route_icon,
                            'route_title' => $user->route_title,
                            'data_id'  => $data_id ?? explode(',',$user->route_parameter),
                            'any_get_method'  => $any_get_mehod,
                        ];
                    } else {
                        $n []= [
                            'route_name' =>  '404',
                            'route_icon' => $user->route_icon,
                            'route_title' => $user->route_title.' (Deleted)',
                            'data_id'  => null,
                            'any_get_method'  => $any_get_mehod,
                        ];
                    }
                }
            }//End Foreach
            $makeMenu []= [
                'route_group_order' => Routegroup::accessColumn($index, 'route_order'),
                'index' => Routegroup::name($index),
                'routes' =>  $n,
            ];
        } //End Foreach
        $keys = array_column($makeMenu, 'route_group_order');
        array_multisort($keys, SORT_ASC, $makeMenu);
        return $makeMenu;
        
    }


}