<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Order Invoice</title>
  <style>
    /** Must be added for multi-language support **/
    @font-face {
      font-family: 'NotoMono-Regular';
      src: url('{{ storage_path('fonts/NotoMono/NotoMono-Regular.ttf') }}') format('truetype');
    }

    /*For Chinese Font support*/
    @font-face {
      font-family: 'NotoSansSC';
      src: url('{{ storage_path('fonts/NotoMono/NotoSansSC-Regular.ttf') }}') format('truetype');
    }

    @font-face {
      font-family: 'SourceSansPro'
        src: url('{{ storage_path('fonts/SourceSansPro/SourceSansPro-Regular.ttf') }}') format('truetype');
    }

    body {
      font-family: 'DejaVu Sans', 'NotoSansSC', 'SourceSansPro';
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border: none;
    }

    td,
    th {
      text-align: left;
      padding: 8px;
      border: 2px solid white;
      background-color: gainsboro;
    }

    th {
      background-color: silver;
      border-bottom: 2px solid black;
    }
  </style>
</head>

<body>
  @php
    $total_price = 0;
    $order = $data->order ?? $data;
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
    <div style="float:left;">
      <h5>{{ trans('app.order') }}: {{ $order->order_number }}</h5>
    </div>
    <h5 style="float:right;">{{ trans('app.order_date') }}: {{ $order->created_at->format('d/m/y') }}</h5>
    <div style="clear:both;"></div>
  </div>

  <div style="width:100%">
    <div style="float:left;">
      <u>{{ trans('app.from') }}</u><br />
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

    <div style="float:right;">
      <u>{{ trans('app.customer') }}</u><br />
      {{ $order->customer->name }}<br />
      {{ $order->customer->email }}<br />
      {{ $order->customer->phone }}
    </div>

    <div style="clear:both;"></div>
  </div>

  <h2 style="text-align: center; text-transform: capitalize;text-decoration: underline">
    {{ trans('app.order_details') }}
  </h2>

  <table>
    <thead>
      <tr>
        <th>{{ trans('app.product') }}</th>
        <th>{{ trans('app.quantity') }}</th>
        <th>{{ trans('app.price') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($order->inventories as $item)
        @php
          $total_price += $item->pivot->unit_price * $item->pivot->quantity;
        @endphp
        <tr>
          <td>{{ $item->title }}</td>
          <td style="text-align: center">{{ $item->pivot->quantity }}</td>
          <td>{{ get_formated_currency($item->pivot->unit_price, 2) }}</td>
        </tr>
      @endforeach

      <tr>
        <td colspan="2">@lang('app.total')</td>
        <td style="border-top: 2px solid black;">{{ get_formated_currency($total_price, 2) }}</td>
      </tr>
      <tr>
        <td colspan="2">@lang('app.tax')</td>
        <td>{{ get_formated_currency($order->taxes, 2) }}</td>
      </tr>
      <tr>
        <td colspan="2">@lang('app.shipping')</td>
        <td>{{ get_formated_currency($order->shipping, 2) }}</td>
      </tr>
      <tr>
        <td colspan="2">@lang('app.grand_total')</td>
        <td>{{ get_formated_currency($order->grand_total, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <div style="width: 100%; margin-top: 10px;">
    <div style="width: 50%; float: left;">
      {{-- Shows payment status of the order --}}
      <strong>@lang('invoice.payment_status')</strong>: {{ $order->paymentStatusName(true) }}<br>

      {{-- Shows payment method name and additional details if the payment method has any --}}
      <strong>@lang('invoice.payment_method')</strong>:{{ $order->paymentMethod->name }}<br>
      @if (optional($order->paymentMethod)->type == \App\Models\PaymentMethod::TYPE_MANUAL)
        @if ($payment_instructions)
          <strong>@lang('invoice.payment_instruction'): </strong> <br>
          {!! $payment_instructions !!}
          <br>
        @endif
      @else
        {{ $order->paymentMethod->instructions }}
      @endif
    </div>

    @if ($stamp_path = optional($order->shop->stampImage)->path)
      <div style="position: fixed; bottom: 30px; width: 50%; float: right; text-align: center;">
        <img src="{{ Storage::path($stamp_path) }}" width="200" style="display: block; margin: 0 auto; margin-top: 25%;" />
      </div>
    @endif

    <div style="clear:both;"></div>
  </div>

  <footer style="position: fixed; bottom: 0; width: 100%;">
    <small>{{ get_platform_title() . ' | ' . url('/') . ' | ' . trans('invoice.footer_note') }}</small>
  </footer>
</body>

</html>
