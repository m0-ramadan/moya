<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDriver
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        if (!$user->driver) {
            return response()->json(['message' => 'يجب أن تكون سائقاً للوصول لهذه الصفحة'], 403);
        }

        if (!$user->driver->is_active) {
            return response()->json(['message' => 'حساب السائق غير نشط'], 403);
        }

        return $next($request);
    }
}
