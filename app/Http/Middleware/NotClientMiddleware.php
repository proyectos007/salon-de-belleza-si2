<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Symfony\Component\HttpFoundation\Response;

class NotClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = FacadesAuth::user();
        if(!$user) {
            return redirect()->route('login');
        }
        
        $role = $user->roles->first();
        if ($role && $role->name == 'client') {
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}
