<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaction Invoice</title>
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
      background-color: white;
      border-bottom: 2px solid #007fff;
    }
  </style>
</head>

<body>
  @php
    //dd($data);
    $transaction = $data['transaction'] ?? $data;
    $invoice_from = $data['invoice_from'] ?? '';
    $invoice_to = $data['invoice_to'] ?? '';
    $footer_string = get_platform_title() . ' | ' . url('/') . ' | ' . trans('invoice.footer_note');
  @endphp
  <div style="width:100%">
    <div style="float:left; margin-top: 20px;">
      {{-- <img src="{{$logo_path}}" width="200" height="75" /> --}}
    </div>
    <div style="float:right; text-style:uppercase">
      <div style="font-size: 1.5rem">@lang('packages.wallet.transactions')</div>
      <div><span style="color:#007fff">@lang('invoice.date')</span>: {{ $transaction->created_at->format('d/m/y') }}</div>
      <div><span style="color:#007fff">@lang('invoice.time')</span>: {{ $transaction->created_at->format('h:i A') }}</div>
    </div>
    <div style="clear:both;"></div>
  </div>
  <div style="width:100%;">
    <div style="float:left; width: 45%;">
      <span style="color:#007fff">@lang('invoice.from')</span>
      <hr style="color:#007fff">
      {{-- Enter address details of the transaction sender here --}}
      @foreach ($invoice_from as $address_line)
        {{ $address_line }}<br>
      @endforeach
    </div>
    <div style="float:right; width: 45%">
      <span style="color:#007fff">@lang('invoice.to')</span>
      <hr style="color: #007fff">
      {{-- Enter details of the transaction reciever here --}}
      @foreach ($invoice_to as $address_line)
        {{ $address_line }}<br>
      @endforeach
    </div>
    <div style="clear:both;"></div>
  </div>

  <table style="margin-top: 10px;">
    <thead>
      <tr>
        <th>@lang('app.description')</th>
        <th>@lang('app.amount')</th>
      </tr>
    </thead>
    <tbody>
      {{-- Enter transaction details here --}}
      <tr>
        <td>{{ $transaction->meta['description'] }}</td>
        <td>{{ get_formated_currency($transaction->amount, 2) }}</td>
      </tr>
      @isset($transaction->meta['fee'])
        <tr>
          <td>@lang('invoice.platform_fee')</td>
          <td>{{ get_formated_currency($transaction->meta['fee'], 2) }}</td>
        </tr>
      @endisset
    </tbody>
  </table>

  <div style="position: fixed; bottom: 5;">
    <span><small>{{ $footer_string }}</small></span>
  </div>
</body>

</html>
