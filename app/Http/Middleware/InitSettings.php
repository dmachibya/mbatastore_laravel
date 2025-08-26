<?php

namespace App\Http\Middleware;

use App\Helpers\ListHelper;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class InitSettings
{
    public function handle($request, Closure $next)
    {
        // Skip initialization during installation
        if ($request->is('install*')) {
            return $next($request);
        }

        // Set system configuration and theme
        setSystemConfig();
        View::addNamespace('theme', theme_views_path());

        // If user is authenticated via web guard
        if (Auth::guard('web')->check()) {
            // Handle impersonation session
            if ($request->session()->has('impersonated')) {
                Auth::onceUsingId($request->session()->get('impersonated'));
            }

            // Skip if in admin or account routes
            if ($request->is('admin/*') || $request->is('account/*')) {
                if ($request->is('admin/setting/system/*')) {
                    // You may add extra handling here if needed
                }
            }

            $user = Auth::guard('web')->user();

            // Set shop config if merchant exists
            if ($user->merchantId()) {
                setShopConfig($user->merchantId());
            }

            // Cache permissions
            $permissions = Cache::remember('permissions_' . $user->id, system_cache_remember_for(), function () {
                return ListHelper::authorizations();
            });

            if (isset($extra_permissions)) {
                $permissions = array_merge($extra_permissions, $permissions);
            }

            config()->set('permissions', $permissions);

            // If super admin, cache slugs
            if ($user->isSuperAdmin()) {
                $slugs = Cache::remember('slugs', system_cache_remember_for(), function () {
                    return ListHelper::slugsWithModulAccess();
                });
                config()->set('authSlugs', $slugs);
            }
        }

        return $next($request);
    }

    private function can_load()
    {
        // Security check for old files or modified files
        // if (
        //     ZCART_MIX_KEY != '017bf8bc885fb37b'
        //     || md5_file(base_path() . '/bootstrap/autoload.php') != '601b5b3a3ffd63e9da1926724abb83c'
        // ) {
        //     die('Did you remove the old files!?');
        // }

        incevioAutoloadHelpers(getMysqliConnection());
    }
}
