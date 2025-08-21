<section>
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <p class="lead">@lang('theme.notify.order_placed_thanks')</p>
        @php
          $orders = isset($orders) ? $orders : [$order]; // Ensure $orders is always an array
        @endphp

        @foreach ($orders as $order)
          @php
            $payment_instructions = null;
            if (optional($order->paymentMethod)->type == \App\Models\PaymentMethod::TYPE_MANUAL) {
                if (vendor_get_paid_directly()) {
                    $payment_method = $order->shop->config->manualPaymentMethods->where('id', $order->payment_method_id)->first();

                    $payment_instructions = optional($payment_method)->pivot->payment_instructions;
                } else {
                    $payment_instructions = get_from_option_table('wallet_payment_instructions_' . $order->paymentMethod->code);
                }
            }
          @endphp

          @if ($payment_instructions)
            <p class="text-primary mb-4">
              <strong>@lang('theme.payment_instruction'): </strong>
              {!! $payment_instructions !!}
            </p>
          @endif

          <p class="text-danger my-4">
            <strong>@lang('theme.payment_status'): </strong> {!! $order->paymentStatusName() !!}
          </p>

          @if ($order->is_digital)
            <p class="my-4">
              @if (\Auth::guard('customer')->check())
                @lang('messages.download_link_loggedin_customer')
              @else
                @lang('messages.download_link_guest_customer')
              @endif
            </p>

            @foreach ($order->inventories as $item)
              <h3>{{ trans('theme.download_links_of') . ': ' . $item->title }}</h3>

              <ul class="my-3">
                @foreach ($item->attachments as $attachment)
                  <li>
                    {{ route('order.attachment.download', ['attachment' => $attachment, 'order' => $order->id, 'inventory' => $item->id]) }}
                    <button class="btn btn-sm ml-3" onclick="navigator.clipboard.writeText('{{ route('order.attachment.download', ['attachment' => $attachment, 'order' => $order->id, 'inventory' => $item->id]) }}')">{{ trans('theme.copy_to_clipboard') }}</button>
                  </li>
                @endforeach
              </ul>
            @endforeach
          @elseif ($order->pickup())
            <p class="fs-3 mb-2">
              <i class="fa fa-map-marker"></i> {{ trans('theme.pickup_from') }} : <br />
              <em>{!! address_str_to_html($order->warehouse->address->toString()) !!}</em>
            </p>

            @if ($order->warehouse->pickup_instruction)
              <p class="text-primary mb-2">
                <strong>{{ trans('app.form.pickup_instruction') }} </strong>
              </p>

              <div class="mb-4">
                {!! $order->warehouse->pickup_instruction !!}
              </div>
            @endif

            @if (is_array($order->warehouse->business_days))
              <p class="fs-3 mb-2 mt-10">
                <i class="fa fa-calendar"></i> {{ trans('theme.notify.business_days') }} : <em>{{ implode(', ', $order->warehouse->business_days) }}</em>
              </p>
            @endif

            @if ($order->warehouse->opening_time && $order->warehouse->close_time)
              <p class="fs-3 mb-2">
                <i class="fa fa-clock-o"></i> {{ trans('theme.pickup_time') }} : <em>{{ $order->warehouse->opening_time }} - {{ $order->warehouse->close_time }}</em>
              </p>
            @endif

            <p class="fs-3 mb-2">
              <i class="fas fa-info-circle"></i> {{ trans('theme.notify.order_number') }} : <em>{{ $order->order_number }}</em>
            </p>
          @else
            <p>
              <i class="fas fa-info-circle"></i> {{ trans('theme.notify.order_will_ship_to') }} : <em>{!! $order->shipping_address !!}</em>
            </p>
          @endif

          <p class="lead text-center my-5">
            @if ($loop->last)
              <a class="btn btn-primary" href="{{ url('/') }}">{{ trans('theme.button.continue_shopping') }}</a>
            @endif

            @if (\Auth::guard('customer')->check())
              <a class="btn btn-default" href="{{ route('order.detail', $order) }}">@lang('theme.button.order_detail')</a>
            @endif
          </p>
        @endforeach
      </div><!-- /.col-md-8 -->
    </div><!-- /.row -->
  </div> <!-- /.container -->
</section>

@if (config('services.google.gtm_container_id'))
  @include('scripts.dataLayer.order_complete')
@endif
