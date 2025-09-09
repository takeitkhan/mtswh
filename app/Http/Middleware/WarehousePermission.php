<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Warehouse;
use App\Models\User;


class WarehousePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()){

            /** ================================================
             * Get the Warehouse details Where the user is entered
             * $request->wh_code = wh_code defined on Routes through route parameter
             *=================================================== */
            //dd(request()->wh_code);
            $checkWarehouse = Warehouse::where('code', $request->wh_code)->orWhere('id', $request->wh_code)->first();
            /**Store warehouse detials to request get */
            $request->attributes->add([
                'warehouse_name' => $checkWarehouse->name ?? null,
                'warehouse_code' => $checkWarehouse->code ?? null,
                'warehouse_id' => $checkWarehouse->id ?? null,
            ]);
            //$request->request->add(['warehouse_code_get_method' => $checkWarehouse->code ?? null]);

            /**End */
            //Check If Have Route permission of this role

            //Current user role of current warehouse
            $user_id = Auth::user()->id;
            $cr = Auth::user()->checkUserWarehouseAccess($user_id);
            //dd($cr);
            /**=====================================================================
             * Check Request Route : Wheather the user has permission in this route
             * ==================================================================*/

            $route = Route::getRoutes()->match($request);
            $currentroute = $route->getName();


             //Check Request Route with Current user Role
             if(auth()->user()->checkUserRoleTypeGlobal() == true){
                $check = true;
            }else {
                if(!empty($cr)){
                    $crole = array($cr->role_id);
                    $request->attributes->add(['authUserRole' => $crole]);
                    $check = auth()->user()->checkRoute($crole, $currentroute) ?? null;
                }else {
                    $check = false;
                }
            }

            //dd(auth()->user()->routeList(array($cr->role_id)));
            if($check == null){
                $request->attributes->add(['hasPermission' => $check]);
            }else{
                //$request->attributes->add(['hasPermission' => $cr]);
                $request->attributes->add(['hasPermission' => $check]);
            }
            $request->attributes->add(['currentUserRole' => $cr->role_id ?? true]);
            return $next($request);
        } else {
            return redirect()->route('login');
        }
        //return $next($request);
    }
}
