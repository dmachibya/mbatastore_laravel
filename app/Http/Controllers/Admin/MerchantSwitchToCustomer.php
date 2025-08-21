<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Jobs\CreateCustomerFromMerchant;

class MerchantSwitchToCustomer extends Controller
{
    /**
     * log in to customer account from vendor
     */
    public function switchToCustomer()
    {
        $user = Auth::guard('web')->user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            Session::put('need_customer_acc', true);

            return redirect()->back()->with('error', trans('messages.customer_acc_not_exist'));
        }

        try {
            Cache::forget('permissions_' . $user->id);              // Clear permissions cache for user
            Auth::guard('web')->logout();                           // Logout the vendor
            Auth::guard('customer')->loginUsingId($customer->id);   // Log in as the customer
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('account', 'dashboard')
            ->with('success', trans('messages.switched_to_customer_successfully'));
    }

    /**
     * Create customer account for the vendor
     *
     * @param Request $request
     * @return void
     */
    public function createCustomer(Request $request)
    {
        try {
            if (!customer_can_register()) {
                $user = Auth::guard('web')->user();

                // Dispatching customer create job
                CreateCustomerFromMerchant::dispatch($user);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        Session::forget('need_customer_acc');

        return redirect()->back()->with('success', trans('messages.customer_acc_created_successfully'));
    }
}
