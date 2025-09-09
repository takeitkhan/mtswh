<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class UserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public $attributes;

    public function handle(Request $request, Closure $next)
    {
        //dd($request->user()->getAuthUserGeneralRole());
        if(Auth::check()){ 

            //Current user role
            //$croles = $request->user()->getUserRole();

            //Check Request Route
            $route = Route::getRoutes()->match($request);
            $currentroute = $route->getName();

            //Check Request Route with Current user Role 
            if(auth()->user()->checkUserRoleTypeGlobal() == true){
                $check = true;
            }else {
                $user_id = Auth::user()->id;
                $cr = $request->user()->checkUserGeneralRole($user_id);
                $crole = array($cr->role_id);
                $request->attributes->add(['authUserRole' => $crole]);
                $check = auth()->user()->checkRoute($crole, $currentroute) ?? null;
            }
            //dd($cr);
            $request->attributes->add(['hasPermission' => $check]);
            $request->attributes->add(['currentUserRole' => $cr->role_id ?? true]);
            
            return $next($request);

        } else {
            return redirect()->route('login');
        }
        //return $next($request);
    }
}
