<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    protected $currency_id;

    public function __construct($resource, $currency_id)
    {
        parent::__construct($resource);
        $this->$currency_id = $currency_id;
    }

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
            'slug' => $this->slug,
            'description' => $this->pivot->item_description,
            'quantity' => $this->pivot->quantity,
            'download_limit' => $this->download_limit,
            'download' => $this->pivot->download,
            'unit_price' => get_formated_currency($this->pivot->unit_price, config('system_settings.decimals', 2), $this->currency_id),
            'total' => get_formated_currency($this->pivot->unit_price * $this->pivot->quantity, config('system_settings.decimals', 2), $this->currency_id),
            'image' => get_inventory_img_src($this, 'small'),
            'attachments' => AttachmentResource::collection($this->attachments),
            'feedback' => $this->when($request->is('api/order/*'), function () {
                $feedback = \App\Models\Feedback::find($this->pivot->feedback_id);

                return $feedback ? new FeedbackResource($feedback) : null;
            }),
        ];
    }
}
