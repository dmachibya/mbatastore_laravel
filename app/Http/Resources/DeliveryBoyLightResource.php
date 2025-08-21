<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryBoyLightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'nice_name'    => $this->nice_name,
            'phone_number' => $this->phone_number,
            'status'       => $this->status == true ? 'Active' : 'Inactive',
            'active'       => $this->status,
            'avatar'       => get_storage_file_url(optional($this->avatarImage)->path, 'small'),
            'avg_rating'   => $this->when($this->feedbacks, $this->feedbacks->avg('rating')),
        ];
    }
}
