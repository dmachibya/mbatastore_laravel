<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Dispute;
use App\Models\Message;
use App\Models\Product;
use App\Models\Visitor;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Provide statistics all over the application
 */
class Statistics
{
    public static function visitor_count($period = null)
    {
        $visitor = new Visitor;

        if (is_null($period)) {
            return $visitor->count();
        } elseif (is_numeric($period)) {
            $date = Carbon::today()->subDays($period)->startOfDay();
        } elseif ($period == 'today') {
            $date = Carbon::today()->startOfDay();
        } else {
            $date = Carbon::today()->startOfDay();
        }

        return $visitor->of($date)->count();
    }

    public static function merchant_count($period = null)
    {
        $merchant = new Merchant;

        if ($period) {
            $date = Carbon::today()->subDays($period);
            $merchant = $merchant->where('created_at', '>=', $date);
        }

        return $merchant->count();
    }

    public static function new_vendor_count($hrs = 24)
    {
        $merchant = new Merchant;

        return $merchant->where('created_at', '>=', Carbon::now()->subHours($hrs))->count();
    }

    public static function customer_count($period = null)
    {
        $customer = new Customer;

        if ($period) {
            $date = Carbon::today()->subDays($period);

            $customer = $customer->where('created_at', '>=', $date);
        }

        return $customer->count();
    }

    public static function stock_out_count()
    {
        return Inventory::mine()->stockOut()->count();
    }

    public static function customer_orders_count($customer)
    {
        return Order::withTrashed()->where('customer_id', $customer)->count();
    }

    public static function total_spent($customer)
    {
        return Order::withTrashed()->where('customer_id', $customer)->sum('total');
    }

    public static function last_sale()
    {
        return Order::mine()->withTrashed()->orderBy('created_at', 'desc')->first();
    }

    public static function todays_sale_amount()
    {
        if (Auth::user()->isFromPlatform()) {
            return Order::withTrashed()->whereDate('created_at', Carbon::today())->sum('total');
        }

        return Order::mine()->withTrashed()->whereDate('created_at', Carbon::today())->sum('total');
    }

    public static function yesterdays_sale_amount()
    {
        if (Auth::user()->isFromPlatform()) {
            return Order::withTrashed()->whereDate('created_at', Carbon::yesterday())->sum('total');
        }

        return Order::mine()->withTrashed()
            ->whereDate('created_at', Carbon::yesterday())->sum('total');
    }

    public static function sales_data_by_period(Carbon $startTime, Carbon $endTime)
    {
        return Order::select('total', 'discount', 'created_at')
            ->mine()->withTrashed() //Include the archived orders also
            ->where('created_at', '<=', $startTime)
            ->where('created_at', '>=', $endTime)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public static function latest_refund_total($period = 15)
    {
        return Refund::mine()->statusOf(Refund::STATUS_APPROVED)
            ->whereDate('updated_at', '>=', Carbon::today()->subDays($period))->sum('amount');
    }

    public static function latest_order_count($period = 15)
    {
        return Order::mine()->withTrashed()
            ->whereDate('created_at', '>=', Carbon::today()->subDays($period))->count();
    }

    public static function todays_order_count()
    {
        return Order::mine()->withTrashed()->whereDate('created_at', Carbon::today())->count();
    }

    public static function todays_all_order_count()
    {
        return Order::query()->whereDate('created_at', Carbon::today())->count();
    }

    public static function yesterday_order_count()
    {
        return Order::mine()->withTrashed()->whereDate('created_at', Carbon::yesterday())->count();
    }

    public static function yesterday_all_order_count()
    {
        return Order::query()->whereDate('created_at', Carbon::yesterday())->count();
    }

    public static function total_order_count()
    {
        return Order::all()->count();
    }

    public static function admin_created_total_product_count()
    {
        return Product::withTrashed()->whereNull('shop_id')->count();
    }

    public static function merchant_created_total_product_count()
    {
        return Product::withTrashed()->whereNotNull('shop_id')->count();
    }


    public static function unfulfilled_order_count()
    {
        return Order::mine()->unfulfilled()->count();
    }

    public static function abandoned_carts_count($period = 15)
    {
        return Cart::mine()->whereDate('created_at', '>=', Carbon::today()->subDays($period))->count();
    }

    public static function shop_user_count($shop_id = null)
    {
        if ($shop_id) {
            return \DB::table('users')->where('shop_id', $shop_id)->count();
        }

        return User::mine()->count();
    }

    public static function shop_inventories_count($shop_id = null)
    {
        if ($shop_id) {
            return \DB::table('inventories')->where('shop_id', $shop_id)->count();
        }

        return Inventory::mine()->count();
    }

    public static function unread_msg_count()
    {
        return \DB::table('messages')->where('shop_id', Auth::user()->merchantId())
            ->where('label', Message::LABEL_INBOX)
            ->where('status', '<', Message::STATUS_READ)->count();
    }

    public static function draft_msg_count()
    {
        return \DB::table('messages')->where('shop_id', Auth::user()->merchantId())
            ->where('label', Message::LABEL_DRAFT)->count();
    }

    public static function spam_msg_count()
    {
        return \DB::table('messages')->where('shop_id', Auth::user()->merchantId())
            ->where('label', Message::LABEL_SPAM)->count();
    }

    public static function trash_msg_count()
    {
        return \DB::table('messages')->where('shop_id', Auth::user()->merchantId())->where('label', Message::LABEL_TRASH)->count();
    }

    public static function open_refund_request_count($shop = null)
    {
        if ($shop && Auth::user()->isFromPlatform()) {
            return Refund::where('shop_id', $shop)->open()->count();
        }

        return Refund::mine()->open()->count();
    }

    public static function refund_request_count($period = null, $shop = null)
    {
        $refund = new Refund;

        if (!Auth::user()->isFromPlatform()) {
            $refund->mine();
        } elseif ($shop) {
            $refund->where('shop_id', $shop);
        }

        if ($period) {
            $date = Carbon::today()->subDays($period);

            $refund = $refund->where('created_at', '>=', $date);
        }

        return $refund->count();
    }

    public static function dispute_count($shop = null, $period = null)
    {
        $dispute = new Dispute;

        if (!Auth::user()->isFromPlatform()) {
            $dispute = $dispute->mine();
        } elseif ($shop) {
            $dispute = $dispute->where('shop_id', $shop);
        }

        if ($period) {
            $date = Carbon::today()->subDays($period);

            $dispute = $dispute->where('created_at', '>=', $date);
        }

        return $dispute->count();
    }

    public static function appealed_dispute_count($shop = null, $period = null)
    {
        $dispute = new Dispute;

        if (!Auth::user()->isFromPlatform()) {
            $dispute = $dispute->mine();
        } elseif ($shop) {
            $dispute = $dispute->where('shop_id', $shop);
        }

        if ($period) {
            $date = Carbon::today()->subDays($period);

            // If include all disputes of every statuses
            return $dispute->where('created_at', '>=', $date)->count();
        }

        return $dispute->statusOf(Dispute::STATUS_APPEALED)->count();
    }

    public static function disputes_by_customer_count($customer, $period = null)
    {
        if ($period) {
            $date = Carbon::today()->subDays($period);

            return \DB::table('disputes')->where('customer_id', $customer)
                ->where('created_at', '>=', $date)->count();
        }

        return \DB::table('disputes')->where('customer_id', $customer)->count();
    }

    public static function pending_approval_count()
    {
        return \DB::table('shops')->where('deleted_at', null)->where('active', '!=', 1)->count();
    }

    public static function pending_verification_count()
    {
        return \DB::table('shops')->where('deleted_at', null)
            ->join('configs', 'configs.shop_id', '=', 'shops.id')
            ->where('configs.pending_verification', 1)->count();
    }
}
