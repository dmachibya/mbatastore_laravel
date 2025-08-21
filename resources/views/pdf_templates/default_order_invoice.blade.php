<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Invoice</title>
  <style>
  /** Must be added for multilanguage support **/
  body { 
    font-family: DejaVu Sans; 
  }
  
  table {
      width: 100%;
      border-collapse: collapse;
      border: none;
  }
  
  td, th {
      text-align: left;
      padding: 8px;
      border: 2px solid white;
      background-color: gainsboro;
  }

  th {
    background-color: white;
    border-bottom: 2px solid #007fff;
  }
  </style>
</head>
<body>
  @php
      $order = $data->order ?? $data;
      $shop = $order->shop;
      $logo_path = isset($shop->logoImage) ? Storage::path($shop->logoImage->path) : null;
      // $stamp_path = Storage::path($shop->stampImage->path);
  
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
    <div style="width:100%">
      <div style="float:left; margin-top: 20px;">
          <img src="{{$logo_path ?? ''}}" width="200" height="75" />
      </div>
      <div style="float:right; text-@langform:uppercase">
        <div style="font-size: 2rem">@lang('invoice.invoice')</div>
        <div><span style="color:#007fff">@lang('invoice.invoice')</span>: {{ $order->order_number }}</div>
        <div><span style="color:#007fff">@lang('invoice.date')</span>: {{ $order->created_at->format('d/m/y') }}</div>
        <div><span style="color:#007fff">@lang('invoice.time')</span>: {{ $order->created_at->format('h:i A') }}</div>
      </div>
      <div style="clear:both;"></div>
    </div>
    <div style="width:100%;">
      <div style="float:left; width: 45%;">
        <span style="color:#007fff">@lang('invoice.from')</span>
        <hr style="color:#007fff">
        @if (isset($order->shop->name) && !empty($order->shop->name))
          <b>{{ $order->shop->name }}</b><br />
        @endif
        @if (isset($order->shop->address->address_line_1) && !empty($order->shop->address->address_line_1))
          {{ $order->shop->address->address_line_1 }}<br />
        @endif
        @if (isset($order->shop->address->address_line_2) && !empty($order->shop->address->address_line_2))
          {{ $order->shop->address->address_line_2 }}<br />
        @endif
        @if (isset($order->shop->address->city->name) && !empty($order->shop->address->city->name))
          {{ $order->shop->address->city->name }}<br />
        @endif
        @if (isset($order->shop->address->state->name) && !empty($order->shop->address->state->name))
          {{ $order->shop->address->state->name }}<br />
        @endif
        @if (isset($order->shop->address->country->name) && !empty($order->shop->address->country->name))
          {{ $order->shop->address->country->name }}<br />
        @endif
      </div>
      <div style="float:right; width: 45%">
        <span style="color:#007fff">@lang('invoice.to')</span>
        <hr style="color: #007fff">
        {{ $order->customer->name }}<br />
        {{ $order->customer->email }}<br />
        {{ $order->customer->phone }}
      </div>
      <div style="clear:both;"></div>
    </div>
  
  <table style="margin-top: 10px;">
    <thead>
      <tr>
          <th>@lang('app.description')</th>
          <th>@lang('app.quantity')</th>
          <th>@lang('app.price')</th>
          <th>@lang('app.total')</th>
      </tr>
    </thead>
    <tbody>
      @php
          $total_price = 0;
      @endphp
      @foreach ($order->inventories as $item)
          @php
              $total_price += $item->pivot->unit_price * $item->pivot->quantity;
          @endphp
          <tr>
          <td>{{ $item->title }}</td>
          <td>{{ $item->pivot->quantity }}</td>
          <td>{{ get_formated_currency($item->pivot->unit_price, 2) }}</td>
          <td>{{ get_formated_currency($item->pivot->unit_price * $item->pivot->quantity, 2) }}</td>
          </tr>
      @endforeach
      <tr>
          <td colspan="2" style="background-color: white;"></td>
          <td>@lang('app.total')</td>
          <td>{{ get_formated_currency($total_price, 2) }}</td>
      </tr>
      <tr>
          <td colspan="2" style="background-color: white;"></td>
          <td>@lang('app.tax')</td>
          <td>{{ get_formated_currency($order->taxes, 2) }}</td>
      </tr>
      <tr>
        <td colspan="2" style="background-color: white;"></td>
        <td>@lang('app.shipping')</td>
        <td>{{ get_formated_currency($order->shipping, 2) }}</td>
      </tr>
      <tr>
        <td colspan="2" style="background-color: white;"></td>
        <td style="background: light#007fff">@lang('app.grand_total')</td>
        <td style="background: light#007fff">{{ get_formated_currency($order->grand_total, 2) }}</td>
      </tr>
    </tbody>
  </table>
  <div style="width: 100%; margin-top: 10px;">
    <div style="width: 50%; float: left;">
      <strong>@lang('invoice.payment_status')</strong>: {{ $order->paymentStatusName(true) }}<br>
      <strong>@lang('invoice.payment_method')</strong>:{{ $order->paymentMethod->name }}<br>
      @if (optional($order->paymentMethod)->type == \App\Models\PaymentMethod::TYPE_MANUAL)
        @if ($payment_instructions)
          <strong>@lang('invoice.payment_instruction'): </strong> <br>
          {!! $payment_instructions !!}
          <br>
        @endif

        @if (isset($payment_method) && optional($payment_method)->pivot->additional_details)
          <strong>@lang('invoice.additional_info')</strong> <br>
          {{ optional($payment_method)->pivot->additional_details }}
        @endif
      @else
       {{ $order->paymentMethod->instructions }}
      @endif
    </div>
    <div style="width: 50%; float: right; text-align: center;">
      <img src="{{ $logo_path ?? '' }}" width="200" height="50" style="display: block; margin: 0 auto; margin-top: 25%;" />
    </div>
    <div style="clear:both;"></div>
  </div>
</body>
</html>
