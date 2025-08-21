<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class FlashdealRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'listings' => 'required_without:featured',
            'featured' => 'required_without:listings'
        ];
    }
}
