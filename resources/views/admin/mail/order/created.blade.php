@component('mail::message')
{{ trans('notifications.order_created.greeting', ['customer' => $order->customer->getName()]) }}

{{ trans('notifications.order_created.message', ['order' => $order->order_number]) }}
<br/>

@if($order->is_digital)
<p class="space-50 mt-3 mb-2">@lang('messages.download_link_of_digital_product')</p>
@foreach($order->inventories as $item)
@foreach ($item->attachments as $attachment)
<a href="{{ route('order.attachment.download', ['attachment' => $attachment, 'order' => $order->id, 'inventory' => $item->id]) }}" class="btn btn-default btn-xs">{!! $item->title !!}</a></br>
@endforeach
@endforeach
@endif
<br/>

@component('mail::button', ['url' => $url, 'color' => 'blue'])
{{ trans('notifications.order_created.button_text') }}
@endcomponent

@include('admin.mail.order._order_detail_panel', ['order_detail' => $order])

{{ trans('messages.thanks') }},<br>
{{ $order->shop->name  . ', ' . get_platform_title() }}
@endcomponent
