<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order' => $this->when(!$request->is('api/order/*'), $this->order),
            'type' => $this->when(!$request->is('api/order/*'), $this->typeName($this->type)),
            'code' => $this->when(!$request->is('api/order/*'), $this->code),
            'additional' => $this->when(!$request->is('api/order/*'), $this->additional_details),
            'name' => $this->name,
        ];
    }
}
