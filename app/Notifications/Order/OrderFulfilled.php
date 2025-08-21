<?php

namespace App\Notifications\Order;

use App\Models\Customer;
use App\Models\Order;
use App\Notifications\Push\HasNotifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\SmsChannel;

class OrderFulfilled extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {   
        //push notification to vendor
        if ($this->order->device_id !== null) {
            HasNotifications::pushNotification(self::toArray($notifiable));
        }

        $channels = ['mail'];

        if ($notifiable instanceof Customer) {
            $channels[] = 'database';
        }

        // Add SMS channel for customers with phone numbers and configured SMS gateway
        if ($notifiable instanceof Customer && $notifiable->phone && $notifiable->smsGateway) {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(get_sender_email(), get_sender_name())
            ->subject(trans('notifications.order_fulfilled.subject', ['order' => $this->order->order_number]))
            ->markdown('admin.mail.order.fulfilled', [
                'url' => get_shop_url($this->order->shop),
                'order' => $this->order
            ]);
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param mixed $notifiable
     * @return string
     */
    public function toSms($notifiable)
    {   
        $notification_message = trans('notifications.order_fulfilled.message', ['order' => $this->order->order_number]) . "\n" . get_shop_url($this->order->shop);

        return $notification_message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'order' => $this->order->order_number,
            'device_id' => $this->order->device_id,
            'status' => $this->order->orderStatus(true),
            'subject' => trans('notifications.order_fulfilled.subject', ['order' => $this->order->order_number]),
            'message' => trans('notifications.order_fulfilled.message', ['order' => $this->order->order_number]),
        ];
    }
}
