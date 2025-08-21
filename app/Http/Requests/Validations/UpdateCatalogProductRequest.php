<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class UpdateCatalogProductRequest extends Request
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

        return [
          'category_list' => 'required',
          'name' => 'required|composite_unique:products,name, '.$id,
          'description' => 'required',
          'active' => 'required',
          'min_price' => 'nullable|numeric|min:0',
          'max_price' => 'nullable|numeric|min:'.$this->min_price ?? 0,
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
