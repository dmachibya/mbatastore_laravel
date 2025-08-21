@component('mail::message')
{{ trans('notifications.vendor_registered.greeting') }}

{!! trans('notifications.vendor_registered.message', ['marketplace' => get_platform_title(), 'shop_name' => $merchant->owns->name, 'merchant_email' => $merchant->email]) !!}
<br/>

@component('mail::button', ['url' => $url, 'color' => 'green'])
{{ trans('notifications.vendor_registered.button_text') }}
@endcomponent

{{ trans('messages.thanks') }},<br>
{{ get_platform_title() }}
@endcomponent
