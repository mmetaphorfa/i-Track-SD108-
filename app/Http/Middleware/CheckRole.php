<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {
        $sessionRole = session('role');

        if (isset($sessionRole) && $sessionRole !== Null) {
            // Assign 'superadmin' role if the user is the first account
            $userRole = Auth::user()->username == '999999999999' && $sessionRole == 'admin' ? 'superadmin' : $sessionRole;

            // Check if the user's role matches the required role
            if (!in_array($userRole, $role)) {
                return to_route('user.dashboard', ['role' => $sessionRole]);
            }
            return $next($request);
        }
        return to_route('user.logout');
    }
}
