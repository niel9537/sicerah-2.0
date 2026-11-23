<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictInspektorat
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role === 'inspektorat') {
            if ($request->routeIs('dashboard', 'landing')) {
                return redirect()->route('prosn.index');
            }
            abort_unless($request->routeIs('prosn.index', 'prosn.export', 'prosn.validasi', 'prosn.validate', 'logout'), 403, 'Akun Inspektorat hanya dapat mengakses eMonev ProSN.');
        }

        return $next($request);
    }
}
