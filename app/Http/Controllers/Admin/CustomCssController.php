<?php

namespace App\Http\Controllers\Admin;

use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Validations\CustomCssRequest;

class CustomCssController extends Controller
{
    use Authorizable;

    /**
     * Display list of custom css
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('admin.customcss.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CustomCssRequest $request)
    {
        $field = 'theme_custom_styling' . Auth::user()->shop_id;

        if (update_option_table_record($field, strip_tags($request->input('theme_custom_css'), "\n\r"))) {
            Cache::forget($field); // Reset the cache

            return redirect()->route('admin.appearance.custom_css')
                ->with('success', trans('messages.custom_css_updated'));
        }

        return redirect()->route('admin.appearance.custom_css')
            ->with('error', trans('messages.failed'));
    }
}
