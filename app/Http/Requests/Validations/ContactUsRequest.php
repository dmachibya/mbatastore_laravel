<?php

namespace App\Http\Requests\Validations;

use App\Http\Requests\Request;

class ContactUsRequest extends Request
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
        // dd($this->all());
        $rules = [
            'name' => 'required',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email',
            'subject' => 'required|max:200',
            'message' => 'required|max:500',
        ];

        if (is_incevio_package_loaded('smartForm')) {
            // $rules['message'] = 'nullable';
            $rules['extra_info'] = 'required|array';

            if (str_contains(url()->previous(), 'selling')) {
                $form = config('system_settings.smart_form_id_for_selling_page');
            } else {
                $form = config('system_settings.smart_form_id_for_contact_us_page');
            }

            $rules = array_merge($rules, get_smart_form_validation_rules($form, 'extra_info'));
        }

        if (config('services.recaptcha.key')) {
            $rules['g-recaptcha-response'] = 'required|recaptcha';
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
            'extra_info.required' => trans('packages.smartForm.form_data_requied_validation_msg'),
        ];
    }
}
