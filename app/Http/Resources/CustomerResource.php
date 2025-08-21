<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $customer_data = [
            'id' => $this->id,
            'name' => $this->name,
            'nice_name' => $this->nice_name,
            'dob' => $this->dob ? $this->dob : null,
            // 'dob' => $this->dob ? date('F j, Y', strtotime($this->dob)) : null,
            'sex' => $this->sex ? trans($this->sex) : null,
            'description' => $this->description,
            'active' => $this->active,
            'email' => $this->email,
            'phone' => $this->when(is_incevio_package_loaded('otp-login'), $this->phone),
            'accepts_marketing' => $this->accepts_marketing,
            'member_since' => optional($this->created_at)->diffForHumans(),
            'avatar' => get_storage_file_url(optional($this->avatarImage)->path, 'small'),
            // 'last_visited_at' => $this->last_visited_at,
            // 'last_visited_from' => $this->last_visited_from,
            'api_token' => $this->when(isset($this->api_token), $this->api_token),
        ];

        if (is_incevio_package_loaded('buyerGroup')) {
            $customer_data = array_merge($customer_data, [
                'buyer_group_id' =>  $this->buyer_group_id,
                'buyer_group_requested_id' =>  $this->buyer_group_requested_id,
                'buyer_group_application_status' =>  $this->buyer_group_application_status,
                'buyer_group_application_details' =>  unserialize($this->buyer_group_application_details),
            ]);
        }

        return $customer_data;
    }
}
