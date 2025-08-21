<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Incevio\Package\SmsGateways\Services\SmsGatewayFactory;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (is_incevio_package_loaded('smsGateways') && method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);

        // For test notifications, use the test phone number
        // $phone = method_exists($notification, 'getTestPhone') ? $notification->getTestPhone() : $notifiable->phone;
        $phone = $notification->phone;
        
        if (!$message || !$phone) {
            return;
        }

        try {
            // Get the SMS gateway configuration from the notifiable entity
            $gateway = method_exists($notifiable, 'smsGateway') ? $notifiable->smsGateway : null;

            // Create the appropriate SMS service using the factory
            $smsService = SmsGatewayFactory::create($gateway);

            // Set the recipient phone number
            $smsService->setRecipient($phone);

            // Set the sender if available from configuration
            $sender = $gateway->credentials['from_number'];
            if ($sender) {
                $smsService->setSender($sender);
            }

            // Send the SMS
            $response = $smsService->sendSms($message);

            if (!$response) {
                Log::error('SMS sending failed for notification: ' . get_class($notification));
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('SMS Channel Error: ' . $e->getMessage());
            return false;
        }
    }
}
