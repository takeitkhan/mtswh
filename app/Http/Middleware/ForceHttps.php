<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
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
        // Force HTTPS in production or when APP_ENV is not local
        if (env('APP_ENV') !== 'local' && !$request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        // Set the scheme for URL generation
        if (!$request->secure()) {
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', 443);
        }

        return $next($request);
    }
}
