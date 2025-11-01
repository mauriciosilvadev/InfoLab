<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LogUserLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && ! Session::has('user_login_logged')) {
            $user = Auth::user();

            activity('user')
                ->withProperties([
                    'user_id' => $user->getAuthIdentifier(),
                    'user_name' => $user->name ?? 'N/A',
                    'user_email' => $user->email ?? 'N/A',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->toISOString(),
                    'login_method' => 'filament_panel',
                ])
                ->event('login')
                ->log("Usuário {$user->name} fez login no sistema");

            Session::put('user_login_logged', true);
        }

        return $response;
    }
}
