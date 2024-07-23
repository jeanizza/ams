<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRoleAndOffice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role, $div_name, $sec_name = null)
    {
        $user = Auth::user();
        if ($user && $user->role === $role && $user->div_name === $div_name) {
            if ($sec_name === null || $user->sec_name === $sec_name) {
                return $next($request);
            }
        }
        
        return abort(403, 'Unauthorized action.');
    }
}
