<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    /**
     * [ajaxGetFromPHPHelper description]
     *
     * @param  string $funcName name of the PHP helper function
     * @param  mix $args     arguments will need to pass to the helper function
     *
     * @return mix         results from PHP Helper function
     */
    public function ajaxGetFromPHPHelper(Request $request)
    {
        if ($request->ajax()) {
            // Sanitize the data
            $allowed_functions = [
                'get_shipping_zone_of',
                'getShippingRates',
                'getShopConfig',
                'get_item_details_of',
                'get_storage_file_url',
                'get_product_img_src',
                'verifyUniqueSlug',
                'getHandelingCostOf',
                'generateCouponCode',
            ];

            $funcName = $request->input('funcName');

            if (!in_array($funcName, $allowed_functions)) {
                return response()->json(['error' => trans('responses.resource_not_found')], 404);
            }

            // Prepare arguments
            $args = $request->input('args');
            $args = is_array($args) ? $args : explode(',', $args);

            $results = call_user_func_array($funcName, $args);

            if (is_object($results)) {
                return json_encode($results);
            }

            return $results;
        }

        return false;
    }

    /**
     * Return Shipping Options
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function filterShippingOptions(Request $request)
    {
        if ($request->ajax()) {
            return filterShippingOptions($request->input('zone'), $request->input('price'), $request->input('weight'));
        }

        return false;
    }
}
