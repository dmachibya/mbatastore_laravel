<?php

namespace App\Http\Resources;

use App\Helpers\Statistics;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'member_since' => date('F j, Y', strtotime($this->created_at)),
            'verified' => $this->isVerified(),
            'verified_text' => $this->verifiedText(),
            'banner_image' => get_cover_img_src($this, 'shop'),
            'sold_item_count' => $this->total_item_sold,
            'total_sold_amount' => $this->total_sold_amount,
            'active_listings_count' => $this->inventories_count,
            'pickup_enabled' => $this->config->pickup_enabled,
            'image' => get_logo_url($this, 'full'),
            'rating' => $this->rating(),
            'feedbacks_count' => $this->rating() ? $this->avgFeedback->count : 0,
            'feedbacks' => FeedbackResource::collection($this->latestFeedbacks),
        ];
    }
}
