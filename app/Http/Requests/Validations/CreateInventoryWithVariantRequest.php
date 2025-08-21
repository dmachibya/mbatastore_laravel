<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateInventoryWithVariantRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $tempObj = json_decode($this->input('product'));
        
        if(!isset($tempObj)){
            $json_compatable_string = $this->makeStringJsonCompatible($this->input('product'));
            $tempObj = json_decode($json_compatable_string);
        }

        if (is_object($tempObj) && $tempObj->id) {
            return $this->user()->shop->canAddThisInventory($tempObj);
        }

        if($tempObj == null & $this->input('product') != null){
            \Log::error('Invalid json string error for string: ' . $this->input('product'));
        }

        return false;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->user(); //Get current user
        Request::merge([
            'shop_id' => $user->shop_id,
            'user_id' => $user->id,
        ]); //Set user_id

        if ($this->listing_type == 'auction') {
            $this->merge([
                'auctionable' => 1,
                'sale_price' => $this->base_price,
                'auction_status' => \Incevio\Package\Auction\Enums\AuctionStatusEnum::RUNNING,
            ]);
        }

        $rules = [
            'title' => 'required',
            'variants.*' => 'required',
            'sku.*' => 'required|distinct|unique:inventories,sku',
            'sale_price.*' => 'bail|required|numeric|min:0',
            'stock_quantity.*' => 'bail|required|integer',
            'offer_price.*' => 'sometimes|nullable|numeric',
            'available_from' => 'nullable|date',
            'offer_start' => 'nullable|required_with:offer_price.*|date',
            'offer_end' => 'nullable|required_with:offer_price.*|date|after:offer_start.*',
            'image.*' => 'mimes:jpg,jpeg,png,gif,svg',
        ];

        if (is_incevio_package_loaded('pharmacy')) {
            $expiry_date_required = get_from_option_table('pharmacy_expiry_date_required', 1);

            $rules['expiry_date'] = (bool)$expiry_date_required ? 'required|date' : 'nullable|date';
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
        $messages = [
            'variants.*.required' => trans('validation.variants_required'),
            'offer_start.*.required_with' => trans('validation.offer_start_required'),
            'offer_start.after' => trans('validation.offer_start_after'),
            'offer_end.required_with' => trans('validation.offer_end_required'),
            'offer_end.after' => trans('validation.offer_end_after'),
        ];

        foreach ($this->sku as $key => $val) {
            $messages['sku.' . $key . '.unique'] = trans('validation.sku-unique', ['attribute' => $key + 1, 'value' => $val]);
            $messages['sku.' . $key . '.distinct'] = trans('validation.sku-distinct', ['attribute' => $key + 1]);
        }

        foreach ($this->offer_price as $key => $val) {
            $messages['offer_price.' . $key . '.numeric'] = $val . ' ' . trans('validation.offer_price-numeric');
        }

        return $messages;
    }

    /**
     * Make string json compatible by removing any unescaped double quotes in the passed json string.
     * As the string contains font-family property, where the font-family value may contain double quotes.
     * Without escaping the double quotes, the json string will be invalid due to encoding issues.
     * @param $string
     * @return string
     */
    private function makeStringJsonCompatible($string)
    {
        $regex = "/font-family: (.*?);/"; // regex to get only font-family property value

        // Replace font-family property value with escaped double quotes
        $filtered_string = preg_replace_callback($regex, function ($matches) { 
            $font_family = $matches[1];
            $font_family_escaped = str_replace('"', '"', $font_family); // Escape unescaped quotation marks

            return "font-family: $font_family_escaped;";
        }, $string);

        return $filtered_string;
    }
}
