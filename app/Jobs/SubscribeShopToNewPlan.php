<?php

namespace App\Jobs;

use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Bus\Dispatchable;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscribeShopToNewPlan
{
    use Dispatchable;

    protected $merchant;
    protected $plan;
    protected $payment_method;

    /**
     * Create a new job instance.
     *
     * @param  User  $merchant
     * @param  string  $plan
     * @param  str/Null  $payment_method
     * @return void
     */
    public function __construct(User $merchant, $plan, $payment_method = null)
    {
        $this->merchant = $merchant;
        $this->plan = $plan;
        $this->payment_method = $payment_method;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // return "top level";
        $shop = $this->merchant->owns;

        // Create subscription intance
        $subscriptionPlan = SubscriptionPlan::findOrFail($this->plan);

        // return $subscriptionPlan;

        $subscription = $shop->newSubscription($subscriptionPlan);

        // Subtract the used trial days with the new subscription
        if ($shop->onGenericTrial()) {
            $trialDays = Carbon::now()->lt($shop->trial_ends_at) ? Carbon::now()->diffInDays($shop->trial_ends_at) : null;
        } else {
            $trialDays = (bool) config('system_settings.trial_days') ? config('system_settings.trial_days') : null;
        }

        // Set trial days
        if ($trialDays) {
            $subscription->trialDays($trialDays);
        } else {
            $subscription->skipTrial();
        }


        // dd($subscription);

        if (!$this->merchant->hasBillingToken()) {
            // dd('No payment method available for merchant.');
        }

        $payment_method = PaymentMethod::find(5);

        // Create subscription
        // dd($this->merchant->email);
        Log::info("subscription about to be created: ", ['payment_method' => $payment_method, 'subscription' => $subscription]);
        try {
            Log::info('hereee DispatchSync reaching here: ', []);
            $subscription = $subscription->create($payment_method, [
                'email' => $this->merchant->email,
            ]);
            Log::info('Subscription Created: ', ['subscription' => $subscription]);

            // Update shop model
            $shop->forceFill([
                'current_billing_plan' => $this->plan,
                'trial_ends_at' => $subscription->trial_ends_at,
            ])->save();
        } catch (IncompletePayment $e) {
            return redirect()->route('cashier.payment', [$e->payment->id, 'redirect' => route('home')]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
