<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if (Auth::user()->role == $role) {
                return $next($request); // <-- IZINKAN MASUK
            }
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin akses.');
    }
}