<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DriverOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }

        $driver = $user->driver;

        if (!$driver) {
            return response()->json(['message' => 'يجب أن تكون سائقاً للوصول لهذه الصفحة'], 403);
        }

        if (!$driver->is_verified) {
            return response()->json([
                'message' => 'حسابك قيد المراجعة',
                'status' => 'pending',
                'estimated_time' => '24 ساعة'
            ], 403);
        }

        if (!$driver->is_active) {
            return response()->json(['message' => 'حسابك غير نشط حالياً'], 403);
        }

        return $next($request);
    }
}
