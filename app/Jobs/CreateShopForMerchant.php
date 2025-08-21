<?php

namespace App\Jobs;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

class CreateShopForMerchant
{
    use Dispatchable;

    protected $merchant;
    protected $request;

    /**
     * Create a new job instance.
     *
     * @param  User  $merchant
     * @param  string  $request
     * @return void
     */
    public function __construct(User $merchant, $request)
    {
        $this->merchant = $merchant;
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (isset($this->request['active'])) {
            $status = $this->request['active'];
        } else {
            $status = config('system_settings.vendor_needs_approval') ? null : 1;
        }

        if (is_subscription_enabled() && (bool) config('system_settings.trial_days')) {
            $trial_ends_at = now()->addDays(config('system_settings.trial_days'));
        }

        if (is_incevio_package_loaded('smartForm') && isset($request['extra_info']['file_paths'])) {
            // Store files and add filepaths to extra_info
            foreach ($this->request['extra_info'] as $key => $value) {
                if(is_base64($value)){
                    $value = create_file_from_base64($key);
                }

                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $directory = 'shop_extra_info/'. $this->merchant->id . '/' . $this->request['shop_name'];
                    $this->request['extra_info']['file_paths'][$key] = $value->storeAs($directory , $value->getClientOriginalName());
                }
            }
        }

        $shopData = [
            'name' => $this->request['shop_name'],
            'description' => $this->request['description'] ?? trans('app.welcome'),
            'legal_name' => $this->request['legal_name'] ?? null,
            'owner_id' => $this->merchant->id,
            'email' => $this->merchant->email,
            'slug' => $this->request['slug'] ?? Str::slug($this->request['shop_name']),
            'external_url' => $this->request['external_url'] ?? null,
            'timezone_id' => config('system_settings.timezone_id'),
            'card_holder_name' => $this->request['name'] ?? null,
            'current_billing_plan' => $this->request['plan'] ?? null,
            'trial_ends_at' => $trial_ends_at ?? null,
            'active' => $status,
            'extra_info' => json_encode($this->request['extra_info'] ?? null),
        ];

        $shop = Shop::create($shopData);

        // configuring The Shop
        $shop->config()->create([
            'support_email' => $this->merchant->email,
            'default_sender_email_address' => $this->merchant->email,
            'support_phone' => isset($this->request['phone']) ? $this->request['phone'] : null,
            'maintenance_mode' => 1,
        ]);

        // Updating shop_id field in user table
        $this->merchant->shop_id = $shop->id;
        $this->merchant->save();

        // Creating WordWide shippingZones for the Shop
        $shop->shippingZones()->create([
            'name' => trans('app.worldwide'),
            'tax_id' => 1,
            'country_ids' => [],
            'state_ids' => [],
            'rest_of_the_world' => true,
        ]);
    }
}
