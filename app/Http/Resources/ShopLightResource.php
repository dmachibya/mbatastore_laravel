<?php

namespace App\Http\Resources;

use App\Helpers\Statistics;
use Illuminate\Http\Resources\Json\JsonResource;
use function Aws\boolean_value;

class ShopLightResource extends JsonResource
{
    /**
     * @var feedback_given
     */
    private $feedback_id;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $feedback_id = null)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);

        $this->resource = $resource;
        $this->feedback_id = $feedback_id;
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
            'name' => $this->name,
            'slug' => $this->slug,
            'verified' => $this->isVerified(),
            'verified_text' => $this->verifiedText(),
            'image' => get_logo_url($this, 'small'),
            'sold_item_count' => $this->total_item_sold,
            'total_sold_amount' => $this->total_sold_amount,
            'active_listings_count' => $this->inventories_count,
            'contact_number' => $this->config->support_phone,
            'email' => $this->email,
            'rating' => $this->rating(),
            'member_since' => date('F j, Y', strtotime($this->created_at)),
            'pickup_enabled' => $this->config->pickup_enabled,
            'feedbacks_count' => $this->rating() ? $this->avgFeedback->count : 0,
            'feedbacks' => $this->when($request->is('api/order/*'), function () {
                $feedback = \App\Models\Feedback::find($this->feedback_id);

                return $feedback ? new FeedbackResource($feedback) : null;
            }),

            $this->mergeWhen($request->is('api/deliveryboy/*'), function () {
                return [
                    'owner' => [
                        'name' => $this->owner->name,
                        'email' => $this->owner->email,
                        'status' => $this->owner->active,
                    ]
                ];
            })
        ];
    }
}
