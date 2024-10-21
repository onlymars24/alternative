<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role1, $role2)
    {
        if (Auth::user() && Auth::user()->role->name == $role1 || Auth::user()->role->name == $role2) {
            return $next($request);
        }
        return response([
            'message' => 'You don\'t have permission to perform this action'
        ], 403);
    }
}
