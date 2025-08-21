<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantRegistrationResource extends JsonResource
{
    /**
     * Construct is empty to create the resource without passing any data
     */
    public function __construct() {
        
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $default_fields = [
            'agree' => [
                'type' => 'bool',
                'required' => true
            ],
            'email' => [
                'type' => 'text',
                'required' => true
            ],
            'plan' => [
                'type' => 'text',
                'required' => true
            ],
            'password' => [
                'type' => 'text',
                'required' => true
            ],
            'password_confirmation' => [
                'type' => 'text',
                'required' => true
            ],
            'shop_name' => [
                'type' => 'text',
                'required' => true
            ],
        ];

        if (is_incevio_package_loaded('smartForm') && config('system_settings.smart_form_id_for_vendor_additional_info'))
        {
            $smart_form_field = smart_form_fields(config('system_settings.smart_form_id_for_vendor_additional_info'));
            $additional_fields = [];

            for ($field_no = 0; $field_no < count($smart_form_field['label']); $field_no += 1)
            {
                $additional_fields[$smart_form_field['label'][$field_no]] = [
                        'type' => $smart_form_field['type'][$field_no],
                        'required' => $smart_form_field['requirement_type'][$field_no] == 'required' ? true : false,
                        'placeholder' => $smart_form_field['placeholder'][$field_no] ?? '',
                        'default_value' => $smart_form_field['default_value'][$field_no] ?? '',
                        'option' => $smart_form_field['option'][$field_no] ?? [],
                    ];
            }

            $default_fields['extra_info'] = $additional_fields;
        }

        return $default_fields;
    }
}
