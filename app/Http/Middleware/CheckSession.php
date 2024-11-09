<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckSession {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $savedSession = session()->get('session_token');

        if (!$user || !$user->session_token || ($user->session_token != $savedSession)) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['Sesi tidak valid, silakan login ulang.']);
        }
        
        return $next($request);
    }
}