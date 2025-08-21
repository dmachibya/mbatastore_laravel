<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use App\Models\Product;

class CreateInventoryRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->shop->canAddThisInventory($this->product_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->user(); // Get current user
        $this->merge([
            'shop_id' => $user->merchantId(),
            'user_id' => $user->id,
        ]);

        if ($this->listing_type == 'auction') {
            $this->merge([
                'auctionable' => 1,
                'sale_price' => $this->base_price,
                'auction_status' => \Incevio\Package\Auction\Enums\AuctionStatusEnum::RUNNING,
            ]);
        }

        $product = Product::find($this->product_id);

        $min_price = get_formated_decimal($product->min_price);
        $max_price = get_formated_decimal($product->max_price);

        $rules = [
            'title' => 'required',
            'sku' => 'bail|required|composite_unique:inventories,sku,shop_id:' .  $user->merchantId(),
            'sale_price' => 'nullable|required_without:base_price|numeric|min:' . $min_price . ($max_price ? '|max:' . $max_price : ''),
            'base_price' => 'nullable|required_without:sale_price|numeric|min:' . $min_price . ($max_price ? '|max:' . $max_price : ''),
            'offer_price' => 'nullable|numeric',
            'available_from' => 'nullable|date',
            'auction_end' => 'nullable|date|after:available_from',
            'offer_start' => 'nullable|date|required_with:offer_price',
            'offer_end' => 'nullable|date|required_with:offer_price|after:offer_start',
            'slug' => 'required|alpha_dash|unique:inventories,slug',
            'image' => 'mimes:jpg,jpeg,png,gif,svg',
        ];

        if ($this->has('digital_file')) {
            $rules['digital_file'] = 'required|file';
        }

        if (is_incevio_package_loaded('pharmacy')) {
            $expiry_date_required = get_from_option_table('pharmacy_expiry_date_required', 1);

            $rules['expiry_date'] = (bool) $expiry_date_required ? 'required|date' : 'nullable|date';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $msg = [
            'offer_start.required_with' => trans('validation.offer_start_required'),
            'offer_start.after_or_equal' => trans('validation.offer_start_after'),
            'offer_end.required_with' => trans('validation.offer_end_required'),
            'offer_end.after' => trans('validation.offer_end_after'),
        ];

        return $msg;
    }
}
