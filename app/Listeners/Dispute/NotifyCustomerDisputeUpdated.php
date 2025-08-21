<?php

namespace App\Listeners\Dispute;

use App\Events\Dispute\DisputeUpdated;
use App\Notifications\Dispute\Updated as DisputeUpdatedNotification;
use App\Services\FCMService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyCustomerDisputeUpdated implements ShouldQueue
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DisputeUpdated  $event
     * @return void
     */
    public function handle(DisputeUpdated $event)
    {
        $customer_token = optional($event->reply->repliable->customer)->fcm_token;

        if (!is_null($customer_token)) {
            FCMService::send($customer_token, [
              'title' => trans('notifications.dispute_updated.subject', ['order_id' => $event->repliable->order->order_number]),
              'body' => trans('notifications.dispute_updated.message', ['order_id' => $event->repliable->order->order_number]),
            ]);
        }

        $event->reply->repliable->customer->notify(new DisputeUpdatedNotification($event->reply));
    }
}
