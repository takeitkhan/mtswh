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
        if (Auth::check()) {

            $user = $request->user();

            // Current route
            $route = Route::getRoutes()->match($request);
            $currentroute = $route->getName();

            if ($user->checkUserRoleTypeGlobal() === true) {
                $check = true;
                $crole = [];
                $roleId = null;
            } else {
                $user_id = $user->id;
                $cr = $user->checkUserGeneralRole($user_id);

                // Protect against null
                if ($cr) {
                    $crole = [$cr->role_id];
                    $roleId = $cr->role_id;
                } else {
                    $crole = [];
                    $roleId = null; // or a default role id if you prefer
                }

                $request->attributes->add(['authUserRole' => $crole]);

                $check = $user->checkRoute($crole, $currentroute) ?? null;
            }

            $request->attributes->add(['hasPermission' => $check]);
            $request->attributes->add(['currentUserRole' => $roleId]);

            return $next($request);
        }

        return redirect()->route('login');
    }
}
