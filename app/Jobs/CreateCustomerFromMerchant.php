<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateCustomerFromMerchant
{
    use Dispatchable;

    protected $merchant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Customer::create([
            'name' => $this->merchant->name,
            'email' => $this->merchant->email,
            'password' => $this->merchant->password,
            'verification_token' => $this->merchant->verification_token,
        ]);
    }
}
