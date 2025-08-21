<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyUserType
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $userType)
    {
        switch ($userType) {
            case 'merchant':
                if ($request->user()->isMerchant()) {
                    return $next($request);
                }
                break;

            case 'admin':
                if ($request->user()->isAdmin()) {
                    return $next($request);
                }
                break;
        }

        if ($request->ajax() || $request->wantsJson()) {
            switch ($userType) {
                case 'merchant':
                    return response('Only the shop owner can access this page.', 402);
                case 'admin':
                    return response('Only the admin users can access this page.', 402);
            }
        }

        return redirect()->route('admin.admin.dashboard');
    }
}
