<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
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
            'slug' => $this->slug,
            'product_id' => $this->product_id,
            'title' => $this->title,
            'condition' => $this->condition,
            // 'attributes' => AttributeLightResource::collection($this->whenLoaded('attributeValues')),
            'has_offer' => $this->hasOffer(),
            'raw_price' => get_formated_value($this->current_sale_price()),
            'currency' => get_system_currency(),
            'currency_symbol' => get_currency_symbol(),
            'price' => get_formated_currency($this->sale_price, config('system_settings.decimals', 2)),
            'offer_price' => $this->hasOffer() ? get_formated_currency($this->offer_price, config('system_settings.decimals', 2)) : null,
            'discount' => $this->hasOffer() ? trans('theme.percent_off', ['value' => $this->discount_percentage()]) : null,
            'offer_start' => $this->hasOffer() ? (string) $this->offer_start : null,
            'offer_end' => $this->hasOffer() ? (string) $this->offer_end : null,

            $this->mergeWhen(is_incevio_package_loaded('auction'), [
                'auctionable' => $this->auctionable ? true : false,
                'auction_start' => $this->available_from,
                'auction_end' => $this->auction_end,
                'auction_status' => $this->auction_status_text,
                'base_price' => get_formated_currency($this->base_price),
                'base_price_raw' => $this->base_price,
            ]),

            'wholesale_price_list' => is_incevio_package_loaded('wholesale') ? get_wholesale_item_prices($this->id) : null,
            'stuff_pick' => $this->stuff_pick,
            'free_shipping' => $this->free_shipping,
            'hot_item' => $this->orders_count >= config('system.popular.hot_item.sell_count', 3) ? true : false,
            'rating' => $this->rating(),
            'feedbacks_count' => $this->rating() ? $this->avgFeedback->count : 0,
            'labels' => $this->getLabels(),
            'listed_at' => date('F j, Y', strtotime($this->available_from)),
            'image' => get_inventory_img_src($this, 'medium'),
        ];
    }
}
