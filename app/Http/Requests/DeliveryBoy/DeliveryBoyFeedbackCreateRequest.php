<?php

namespace App\Http\Requests\DeliveryBoy;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeliveryBoyFeedbackCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
        } else if (Auth::guard('api')->check()) {
            $customer = Auth::guard('api')->user();
        }

        if (isset($customer) && $customer instanceof Customer) {
            // Set customer_id
            $this->merge(['customer_id' => $customer->id]);

            return $this->route('order')->customer_id == $customer->id;
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
        return [
          'rating' => 'required|numeric|between:1,5',
          'comment' => 'nullable|string|min:10|max:250',
        ];
    }
}
