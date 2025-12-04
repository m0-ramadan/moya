<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('lang', 'en'); // Default to 'en' if not provided
        App::setLocale(in_array($lang, ['en', 'ar']) ? $lang : 'ar');
        
        return $next($request);
    }
}
