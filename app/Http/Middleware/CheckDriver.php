<?php

namespace App\Http\Middleware;

use App\Models\Driver;
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
        if (!Driver::where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'يجب أن تكون سائقاً للوصول لهذه الصفحة'], 403);
        }

        if (!Driver::where('user_id', $user->id)->first()->is_active) {
            return response()->json(['message' => 'حساب السائق غير نشط'], 403);
        }

        return $next($request);
    }
}
