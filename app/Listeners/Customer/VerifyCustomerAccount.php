<?php

namespace App\Listeners\Customer;

use App\Models\Customer;
use App\Events\Customer\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Customer\Registered as CustomerRegister;

class VerifyCustomerAccount implements ShouldQueue
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    public $user;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        // $request->user()->markEmailAsVerified();

        $customer = Customer::where('email', $this->user->email)->first();

        $customer->verification_token = null;

        $customer->save();
    }
}
