<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderUpdated;
use App\Notifications\Order\OrderUpdated as OrderUpdatedNotification;
use App\Services\FCMService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Notifications\Notification;
use Notification;

class NotifyCustomerOrderUpdated implements ShouldQueue
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
     * @param  OrderUpdated  $event
     * @return void
     */
    public function handle(OrderUpdated $event)
    {
        // firebase notification send to delivery body
        $deliveryBoy_token = optional($event->order->deliveryBoy)->fcm_token;

        if (!is_null($deliveryBoy_token)) {
            FCMService::send($deliveryBoy_token, [
                'title' => trans('notifications.order_updated.subject', ['order' => $event->order->order_number]),
                'body' => trans('notifications.order_updated.message', ['order' => $event->order->order_number]),
            ]);
        }

        if ($event->notify_customer) {

            $customer_token = optional($event->order->customer)->fcm_token;

            if (!is_null($customer_token)) {
                FCMService::send($customer_token, [
                    'title' => trans('notifications.order_updated.subject', ['order' => $event->order->order_number]),
                    'body' => trans('notifications.order_updated.message', ['order' => $event->order->order_number]),
                ]);
            }

            if (! config('system_settings')) {
                setSystemConfig($event->order->shop_id);
            }

            // Set shop configuration
            if ($event->order->shop_id && !config('shop_settings')) {
                setSystemConfig($event->order->shop_id);
            }

            if ($event->order->customer_id) {
                $event->order->customer->notify(new OrderUpdatedNotification($event->order));
            } elseif ($event->order->email) {
                Notification::route('mail', $event->order->email)
                    // ->route('nexmo', '5555555555')
                    ->notify(new OrderUpdatedNotification($event->order));
            }
        }
    }
}
