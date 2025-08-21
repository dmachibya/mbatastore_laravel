<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class UpdateSystemConfigRequest extends Request
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
        // Set null when the smart_form_id_for_vendor_additional_info is not selected
        if (!$this->has('smart_form_id_for_vendor_additional_info')) {
            $this->merge(['smart_form_id_for_vendor_additional_info' => null]);
        }

        if (!$this->has('smart_form_id_for_customer_registration_form')) {
            $this->merge(['smart_form_id_for_customer_registration_form' => null]);
        }

        $rules = [];

        $rules['smart_form_id_for_vendor_additional_info'] = 'nullable|integer';

        $rules['smart_form_id_for_customer_registration_form'] = 'nullable|integer';

        return $rules;
    }
}
