<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use App\Models\Inventory;

class UpdateProductRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('product');
        $shop_id = $this->user()->merchantId(); //Get current user's shop_id

        Request::merge([
          'shop_id' => $shop_id,
          'user_id' => $this->user()->id,
        ]);


        if (!$this->input('key_features')) {
            $this->merge(['key_features' => null]);
        }

        if (!$this->input('linked_items')) {
            $this->merge(['linked_items' => null]);
        }

        $inventoryId = Inventory::where('product_id', $id)->pluck('id')->first();

        $rules = [
          'category_list' => 'required',
          'name' => 'required',
          'sale_price' => 'required|numeric|min:0',
          'offer_price' => 'nullable|numeric',
          'available_from' => 'nullable|date',
          'min_price' => 'nullable|numeric|min:0',
          'max_price' => 'nullable|numeric|min:' . $this->min_price ?? 0,
          'offer_start' => 'nullable|date|required_with:offer_price',
          'offer_end' => 'nullable|date|required_with:offer_price|after:offer_start',
          'image' => 'mimes:jpg,jpeg,png,gif,svg',
        ];

        $rules['sku'] = 'bail|required|composite_unique:inventories,sku,shop_id:' . $shop_id . ',' . $inventoryId;
        $rules['slug'] = 'bail|required|alpha_dash|unique:inventories,slug, ' . $inventoryId;

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
        return [
            'category_list.required' => trans('validation.category_list_required'),
            'offer_start.after_or_equal' => trans('validation.offer_start_after'),
            'required_with.required' => trans('validation.offer_end_required'),
            'offer_end.after' => trans('validation.offer_end_after'),
        ];
    }
}
