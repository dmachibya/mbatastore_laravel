<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request to set locale for api requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $localization = $request->header('Accept-Language');
        $available_languages = ListHelper::availableLocales()->pluck('code')->toArray();
        $localization = in_array($localization, $available_languages, true) ? $localization : config('system_settings.default_language');
        
        app()->setLocale($localization);

        return $next($request);
    }
}
