<?php

namespace App\Http\Middleware;

use Closure;

class LanguageSwitcher
{
    public function handle($request, Closure $next)
    {
        app()->setLocale(session()->has('applocale') ? session()->has('applocale') : 'ro');
        app()->setLocale(session()->has('locale') ? session()->has('locale') : 'ro');

        return $next($request);
    }
}