@component('mail::message')
{{ trans('notifications.shop_down_for_maintenance.greeting', ['merchant' => $shop->owner->getName()]) }}

{{ trans('notifications.shop_down_for_maintenance.message', ['shop_name' => $shop->name]) }}
<br/>

@component('mail::button', ['url' => $url, 'color' => 'blue'])
{{ trans('notifications.shop_down_for_maintenance.button_text') }}
@endcomponent

{{ trans('messages.thanks') }},<br>
{{ get_platform_title() }}
@endcomponent
