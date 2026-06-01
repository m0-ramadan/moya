<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminHasDashboardPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth('admin')->user();

        if (! $admin) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        if (! admin_can_access_route($request->route()?->getName(), $admin)) {
            abort(403, 'ليس لديك الصلاحية للوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
