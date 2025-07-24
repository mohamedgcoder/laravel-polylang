<?php

namespace MohamedAhmed\LaravelPolyLang\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = $request->header('Accept-Language', config('app.locale'));

        if (in_array($locale, config('polylang.supported_locales', ['en']))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
