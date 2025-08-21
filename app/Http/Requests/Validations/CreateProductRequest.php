<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateProductRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        incevioAutoloadHelpers(getMysqliConnection());
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->user(); // Get current user
        Request::merge([
            'shop_id' => $user->merchantId(),
            'user_id' => $user->id,
        ]);

        // Set slug
        // if (!$this->has('slug')) {
        //     Request::merge(['slug' => convertToSlugString($this->input('name'), $this->input('gtin'))]);
        // }

        return [
            'category_list' => 'required',
            'name' => 'required',
            'slug' => 'required|unique:products',
            'skus.*' => 'required|distinct|unique:inventories,sku',
            'description' => 'required',
            'active' => 'required',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:' . $this->min_price ?? 0,
            'images.*' => 'mimes:jpg,jpeg,png,gif,svg',
            'sku' => 'bail|required|composite_unique:inventories,sku,shop_id:' . $user->merchantId(),
            'sale_price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric',
            'available_from' => 'nullable|date',
            'offer_start' => 'nullable|date|required_with:offer_price',
            'offer_end' => 'nullable|date|required_with:offer_price|after:offer_start',
        ];
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
            'offer_start.required_with' => trans('validation.offer_start_required'),
            'offer_start.after_or_equal' => trans('validation.offer_start_after'),
            'offer_end.required_with' => trans('validation.offer_end_required'),
            'offer_end.after' => trans('validation.offer_end_after'),
        ];
    }
}
