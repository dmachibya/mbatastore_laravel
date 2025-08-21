<?php

namespace App\Http\Controllers\Storefront;

use App\Models\Inventory;
use App\Models\Wishlist;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, Inventory $item)
    {
        $customer_id = Auth::guard('customer')->user()->id;

        $item_in_wishlist = Wishlist::where('inventory_id', $item->id)
            ->where('customer_id', $customer_id)->first();

        // Item already in cart
        if ($item_in_wishlist) {
            return response()->json(['message' => trans('app.item_already_in_wishlist')], 409);
        }

        $wishlist = new Wishlist;
        $add = $wishlist->updateOrCreate([
            'inventory_id'   =>  $item->id,
            'product_id'   =>  $item->product_id,
            'customer_id' => $request->user()->id,
        ]);

        if ($add) {
            return response()->json([
              'wishlist' => $wishlist->toArray(),
              'item' => $item->toArray(),
            ], 200);
        }

        return response()->json(null, 404);
        //return back()->with('success',  trans('theme.notify.item_added_to_wishlist'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request, Wishlist $wishlist)
    {
        $this->authorize('remove', $wishlist);

        $wishlist->forceDelete();

        return back()->with('success', trans('theme.notify.item_removed_from_wishlist'));
    }
}
