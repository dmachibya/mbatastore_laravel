<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class CreateCatalogProductRequest extends Request
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
        Request::merge(['shop_id' => $this->user()->merchantId()]); //Set shop_id

        return [
          'category_list' => 'required',
          'name' => 'required|unique:products',
          'slug' => 'required|unique:products',
          'description' => 'required',
          'active' => 'required',
          'min_price' => 'nullable|numeric|min:0',
          'max_price' => 'nullable|numeric|min:' . $this->min_price ?? 0,
          'images.*' => 'mimes:jpg,jpeg,png,gif,svg',
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
        ];
    }
}
