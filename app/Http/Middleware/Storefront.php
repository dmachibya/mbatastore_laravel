<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ListHelper;
use Illuminate\Http\Response;
use App\Jobs\UpdateVisitorTable;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Services\ResponseManipulation;
use Illuminate\Support\Facades\Session;

class Storefront
{
    /**
     * Handle an incoming request. Supply important data to all views.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check platform maintenance_mode
        if (config('system_settings.maintenance_mode')) {
            return response()->view('errors.503', [], 503);
        }

        // Skip when ajax request
        if ($request->ajax()) {
            return $next($request);
        }

        // Push an empty value of 0 to card session if not exist
        if (Session::missing('session_carts')) {
            Session::push('session_carts', 0);
        }

        // $expires = system_cache_remember_for();

        // View::share('active_announcement', ListHelper::activeAnnouncement());

        if (active_theme() == 'legacy') {
            View::share('featured_categories', get_featured_category());
        }

        View::share('promotional_tagline', get_promotional_tagline());
        View::share('pages', ListHelper::pages(\App\Models\Page::VISIBILITY_PUBLIC));
        View::share('all_categories', ListHelper::categoriesForTheme(true));
        View::share('search_category_list', ListHelper::search_categories());
        View::share('recently_viewed_items', ListHelper::recentlyViewedItems());
        View::share('cart_item_count', cart_item_count());
        View::share('wishlist_item_count', wishlist_item_count());
        View::share('hidden_menu_items', hidden_menu_items());
        View::share('top_bar_banner', get_top_bar_banner_data());

        // View::share('top_vendors', ListHelper::top_vendors(5));

        // Last Announcement
        if (is_incevio_package_loaded('announcement')) {
            View::share('public_announcements', get_public_announcements());
        }

        // Trending Search Keywords
        if (is_incevio_package_loaded('trendingKeywords')) {
            $trending_keywords = Cache::rememberForever('trending_keywords', function () {
                return get_from_option_table('trendingKeywords_keywords', []);
            });

            View::share('trending_keywords', $trending_keywords);
        }

        // $languages = \App\Language::orderBy('order', 'asc')->active()->get();

        // Update the visitor table for state
        if (config('report.collect_visitor_data')) {
            $ip = get_visitor_IP();

            UpdateVisitorTable::dispatch($ip);
        }

        return $this->insertIntoViewResponse($next($request));
    }

    /**
     * Insert Important content Into View Response
     *
     * @return  \Illuminate\Http\Request  $request
     */
    private function insertIntoViewResponse($response)
    {
        if (!$response instanceof Response) {
            return $response;
        }

        $contents = [
            '</head>' => [
                view('analytic_script')->render(),
            ],
            '</head>' => view('front_admin_topnav')->render(),
            // '</body>' => view('cookie_consent')->render(),
        ];

        foreach ($contents as $tag => $content) {
            $manipulator = new ResponseManipulation($response, $tag, $content);

            $response = $manipulator->getResponse();
        }

        return $response;
    }
}
