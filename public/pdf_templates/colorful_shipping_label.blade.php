<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipping Label</title>
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

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 10px;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
    }

    .top-margined {
      margin-top: 20px;
    }

    .col-12 {
      width: 100%;
    }

    .col-md-12 {
      width: 100%;
    }

    .table {
      border-collapse: separate;
      border-spacing: 0 5px;
      width: 100%;
    }

    .table thead tr th {
      background-color: #3498db;
      padding: 10px;
    }

    .table tbody tr {
      background-color: #f1f1f1;
    }

    .table tbody tr:nth-child(even) {
      background-color: #e6e6e6;
    }

    .table td {
      padding: 10px;
    }

    .table td:first-child {
      border-top-left-radius: 5px;
      border-bottom-left-radius: 5px;
    }

    .table td:last-child {
      border-top-right-radius: 5px;
      border-bottom-right-radius: 5px;
    }

    .from-to {
      display: flex;
      justify-content: space-between;
      width: 100%;
    }

    .from-to>div {
      width: 50%;
    }

    .font-weight-bold {
      font-weight: bold;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }
  </style>
</head>

<body>
  @php
    $order = $data->order ?? $data;
  @endphp
  <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG((string) $order->id, 'C39+') }}" alt="barcode" /> <br>
  @lang('app.order') : {{ $order->order_number }}
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <h2 class="text-primary">{{ $order->shop->name }}</h2>
      </div>
    </div>
    <div style="width:100%">
      <div style="float: left; font-size: 18px">
        <u class="text-info">{{ trans('app.customer') }}</u><br />
        {{ $order->customer->name }}<br />
        {{ $order->customer->email }}<br />
        {{ $order->customer->phone }}
      </div>
      <div style="float: right; font-size: 18px">
        <u class="text-info">{{ trans('app.shop_address') }}</u><br />
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
      <div style="clear: both"></div>
    </div>

    <div class="row top-margined">
      <div class="col-md-12">
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="font-weight-bold">{{ trans('app.product') }}</th>
              <th class="font-weight-bold">{{ trans('app.quantity') }}</th>
              <th class="font-weight-bold text-right">{{ trans('app.price') }}</th>
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
                <td class="text-right">{{ get_formated_currency($item->pivot->unit_price, 2) }}</td>
              </tr>
            @endforeach
            <tr>
              <td class="font-weight-bold" colspan="2">{{ trans('app.total') }}</td>
              <td class="text-right">{{ get_formated_currency($order->total, 2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-12">
        <table class="table table-striped">
          <tbody>
            <tr>
              <td class="font-weight-bold">{{ trans('app.tax') }}</td>
              <td class="text-right">{{ get_formated_currency($order->taxes, 2) }}</td>
            </tr>
            <tr>
              <td class="font-weight-bold">{{ trans('app.shipping') }}</td>
              <td class="text-right">{{ get_formated_currency($order->shipping, 2) }}</td>
            </tr>
            <tr>
              <td class="font-weight-bold">{{ trans('app.grand_total') }}</td>
              <td class="text-right">{{ get_formated_currency($order->grand_total, 2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
