<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdatePromotionalTaglineRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        incevioAutoloadHelpers(getMysqliConnection());
        return Auth::user()->isFromPlatform();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
           'text' => 'required_with:action_url|max:60',
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
            // 'featured_categories.required' => trans('validation.category_list_required'),
        ];
    }
}
