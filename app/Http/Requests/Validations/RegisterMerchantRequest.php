<?php

namespace App\Http\Requests\Validations;

use App\Models\Role;
use App\Http\Requests\Request;

class RegisterMerchantRequest extends Request
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
        $this->merge(['role_id' => Role::MERCHANT]);

        $rules =  [
            'name' => 'required|max:255',
            'shop_name' => 'required|string|max:255|unique:shops,name',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];

        if (config('system_settings.show_vendor_terms_and_conditions')) {
            $rules['agree'] = 'required';
        }

        if (is_subscription_enabled()) {
            $rules['plan'] = 'required';
        }

        // When recaptcha in configured and the call is not from api
        if (config('services.recaptcha.key') && !$this->is('api/vendor/*')) {
            $rules['g-recaptcha-response'] = 'required|recaptcha';
        }

        if (is_incevio_package_loaded('otp-login')) {
            $rules['phone'] = 'required|string|unique:users';
        }

        if (is_incevio_package_loaded('smartForm') && config('system_settings.smart_form_id_for_vendor_additional_info')) {
            // $rules['extra_info'] = 'required|array';
            $rules = array_merge($rules, get_smart_form_validation_rules(config('system_settings.smart_form_id_for_vendor_additional_info'), 'extra_info'));
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
            'email.unique' => trans('validation.register_email_unique'),
            'extra_info.required' => trans('packages.smartForm.form_data_requied_validation_msg'),
        ];
    }
}
