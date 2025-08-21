<?php

namespace App\Http\Resources;

use App\Models\Currency;
use App\Models\System;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use App\Helpers\ListHelper;

class SystemConfigResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    // declaring it outside the if block to avoid undefined error. Need to consider conditional merging in return statement
    $selectedCurrencyDetails = null;

    // Get the api compatibilities settings
    if ($request->is('api/system_configs')) {
      $compatibility = System::$api_compatibility['customer'];

      if (is_incevio_package_loaded('dynamic-currency')) {
        $ip = get_visitor_IP();

        $dynamicCurrencyId = DB::table('visitors')
          ->where('ip', $ip)->value('selected_currency_id');

        if ($dynamicCurrencyId) {
          $selectedCurrencyDetails = Currency::where('id', $dynamicCurrencyId)->first();
        }
      }
    } elseif ($request->is('api/deliveryboy/*')) {
      $compatibility = System::$api_compatibility['delivery'];
    } elseif ($request->is('api/vendor/*')) {
      $compatibility = System::$api_compatibility['vendor'];
    }

    return [
      "maintenance_mode" => (bool) $this->maintenance_mode,
      // "install_verion" => System::VERSION, // Need to fix the spelling in app end also, will be removed soon
      "install_version" => System::VERSION,
      "compatible_app_version" => $this->when(isset($compatibility), $compatibility),
      "name" => $this->name,
      "slogan" => $this->slogan,
      "legal_name" => $this->legal_name,
      "platform_logo" => get_logo_url('system', 'full'),
      "email" => $this->email,
      "worldwide_business_area" => (bool) $this->worldwide_business_area,
      "timezone_id" => $this->timezone_id,
      "currency_id" => $this->currency_id,
      "default_language" => $this->default_language,
      "ask_customer_for_email_subscription" => (bool) $this->ask_customer_for_email_subscription,
      "can_cancel_order_within" => $this->can_cancel_order_within,
      "support_phone" => $this->support_phone,
      "support_phone_toll_free" => $this->support_phone_toll_free,
      "support_email" => $this->support_email,
      "facebook_link" => $this->facebook_link,
      "google_plus_link" => $this->google_plus_link,
      "twitter_link" => $this->twitter_link,
      "pinterest_link" => $this->pinterest_link,
      "instagram_link" => $this->instagram_link,
      "youtube_link" => $this->youtube_link,
      "length_unit" => $this->length_unit,
      "weight_unit" => $this->weight_unit,
      "valume_unit" => $this->valume_unit,
      "decimals" => $this->decimals,
      "show_currency_symbol" => (bool) $this->show_currency_symbol,
      "show_space_after_symbol" => (bool) $this->show_space_after_symbol,
      "max_img_size_limit_kb" => $this->max_img_size_limit_kb,
      "show_item_conditions" => (bool) $this->show_item_conditions,
      "address_default_country" => $this->address_default_country,
      "address_default_state" => $this->address_default_state,
      "show_address_title" => (bool) $this->show_address_title,
      "address_show_country" => (bool) $this->address_show_country,
      "address_show_map" => (bool) $this->address_show_map,
      "allow_guest_checkout" => (bool) $this->allow_guest_checkout,
      "enable_chat" => (bool) $this->enable_chat,
      "vendor_get_paid" => (bool) vendor_get_paid_directly(),
      "currency" => [
        'name' => $this->currency['name'],
        'iso_code' => $this->currency['iso_code'],
        "symbol" => $this->currency['symbol'],
        "symbol_first" => (bool) $this->currency['symbol_first'],
        "subunit" => $this->currency['subunit'],
        "decimal_mark" => $this->currency['decimal_mark'],
        "thousands_separator" => $this->currency['thousands_separator'],
      ],
      "selected_currency" => $selectedCurrencyDetails ? [
        'name' => $selectedCurrencyDetails->name,
        'iso_code' => $selectedCurrencyDetails->iso_code,
        "symbol" => $selectedCurrencyDetails->symbol,
        "symbol_first" => (bool) $selectedCurrencyDetails->symbol_first,
        "subunit" => $selectedCurrencyDetails->subunit,
        "decimal_mark" => $selectedCurrencyDetails->decimal_mark,
        "thousands_separator" => $selectedCurrencyDetails->thousands_separator,
      ] : null,
      "active_languages" => ListHelper::availableLocales()->pluck('language', 'code')->toArray(),

      // "smart_form_id_for_customer_registration_form" => $this->smart_form_id_for_customer_registration_form,
      "smart_form_id_for_customer_registration_form" => "2",
      "show_customer_terms_and_conditions" => $this->show_customer_terms_and_conditions,
      "show_vendor_terms_and_conditions" => $this->show_vendor_terms_and_conditions,
      "smart_form_id_for_vendor_additional_info" => $this->smart_form_id_for_vendor_additional_info,
      'smart_form_id_for_selling_page' => "3",
      // 'smart_form_id_for_selling_page' => $this->smart_form_id_for_selling_page,
      'smart_form_id_for_contact_us_page' => "4",
      // 'smart_form_id_for_contact_us_page' => $this->smart_form_id_for_contact_us_page,
      // 'publicly_show_affiliate_commission' => $this->publicly_show_affiliate_commission,
      'publicly_show_affiliate_commission' => false,
    ];
  }
}
