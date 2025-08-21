<head>
  <meta charset="utf-8" />
</head>
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
  }

  td,
  th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
  }
</style>

@php
  $order = $data->order ?? $data;
@endphp
<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG((string) $order->id, 'C39+') }}" alt="barcode" />
<div style="width:100%; text-align:center; background-color:lightgrey;">
  <h5>{{ trans('app.shipping_label') }}</h5>
</div>
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


<h4 style="width:100%; text-align:center; background-color:lightgrey;">Delivered Products</h4>
<table>
  <thead>
    <tr>
      <th>{{ trans('app.product') }}</th>
      <th>{{ trans('app.quantity') }}</th>
      <th>{{ trans('app.price') }}</th>
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
      </tr>
    @endforeach
    <tr>
      <td colspan="2" style="text-align: right;">{{ trans('app.total') }}</td>
      <td>{{ get_formated_currency($total_price, 2) }}</td>
    </tr>
  </tbody>
</table>


<h4 style="width:100%; text-align:center; background-color:lightgrey;">Cost Distribution</h4>
<table class="table">
  <tbody>
    <tr>
      <td>{{ trans('app.total') }}</td>
      <td>{{ get_formated_currency($order->total, 2) }}</td>
    </tr>
    <tr>
      <td>{{ trans('app.tax') }}</td>
      <td>{{ get_formated_currency($order->taxes, 2) }}</td>
    </tr>
    <tr>
      <td>{{ trans('app.shipping') }}</td>
      <td>{{ get_formated_currency($order->shipping, 2) }}</td>
    </tr>
    <tr>
      <td style="border: 2px solid black;"><b>{{ trans('app.grand_total') }}</b></td>
      <td style="border: 2px solid black;"><b>{{ get_formated_currency($order->grand_total, 2) }}</b></td>
    </tr>
    <tr>
      <td>{{ trans('app.shipping_weight') }}</td>
      <td>{{ number_format((float) $order->shipping_weight, 2, '.', '') . config('system_settings.weight_unit') }}</td>
    </tr>
  </tbody>
</table>
