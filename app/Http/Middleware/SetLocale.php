<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales', ['ar', 'en']);

        $locale = session('locale');

        if (!$locale) {
            $locale = $request->getPreferredLanguage($supportedLocales);
        }

        if (!$locale || !in_array($locale, $supportedLocales)) {
            $locale = 'ar';
        }

        App::setLocale($locale);

        session(['locale' => $locale]);

        return $next($request);
    }
}
