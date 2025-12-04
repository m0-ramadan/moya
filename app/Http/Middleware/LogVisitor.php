<?php

namespace App\Http\Middleware;

use Log;

use Closure;
use App\Models\Visitor;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Torann\GeoIP\Facades\GeoIP;

class LogVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $ip = $request->ip();
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());

            // جلب معلومات الـ GeoIP (لو مثبت torann/geoip)
            $geo = null;
            if (class_exists(\Torann\GeoIP\Facades\GeoIP::class)) {

                try {
                    $geo = GeoIP::getLocation($ip);
                } catch (\Throwable $e) {
                    $geo = null;
                }
            }

            Visitor::create([
                'ip' => $ip,
                'host' => gethostbyaddr($ip) ?: null,
                'method' => $request->method(),
                'path' => $request->path(),
                'full_url' => $request->fullUrl(),
                'referer' => $request->header('referer'),
                'user_agent' => $request->userAgent(),
                'browser' => $agent->browser(),
                'browser_version' => $agent->version($agent->browser()),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
                'is_mobile' => $agent->isMobile(),
                'is_tablet' => $agent->isTablet(),
                'is_desktop' => !$agent->isMobile() && !$agent->isTablet(),
                'is_bot' => $agent->isRobot(),
                'country' => $geo['country'] ?? null,
                'country_iso' => $geo['country_code'] ?? null,
                'region' => $geo['state'] ?? null,
                'city' => $geo['city'] ?? null,
                'latitude' => $geo['lat'] ?? null,
                'longitude' => $geo['lon'] ?? null,
                'timezone' => $geo['timezone'] ?? null,
                'headers' => json_encode(collect($request->headers->all())
                    ->map(fn($v) => count($v) === 1 ? $v[0] : $v)
                    ->toArray()),
                'query' => json_encode($request->query()),

                'session_id' => session()->getId(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Visitor logging failed: ' . $e->getMessage());
        }

        return $response;
    }
}
