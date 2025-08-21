<?php

namespace App\Http\Requests\Validations;

use App\Models\Inventory;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ProductSearchRequest extends Request
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
        return [
            'q' => 'required',
            'price_min' => 'numeric',
            'price_max' => 'numeric',
            'has_offers' => 'sometimes|accepted',
            'new_arrivals' => 'sometimes|accepted',
            'free_shipping' => 'sometimes|accepted',
            'item_condition' => Rule::in(Inventory::CONDITIONS),
            'sort_by' => Rule::in(['price_asc', 'price_desc', 'newest', 'oldest']),
        ];
    }
}
