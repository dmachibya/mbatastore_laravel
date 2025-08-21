<h3 class="widget-title">{{ trans('theme.payment_options') }}</h3>

<div class="mb-4">
  @php
    $config_shop = vendor_get_paid_directly() ? $shop : null;

    // When admin get paid but still give option to vendors on/off a active payment method.
    $active_payment_methods = isset($shop) && !vendor_get_paid_directly() && vendor_can_on_off_payment_method() ? $shop->paymentMethods->pluck('id')->toArray() : [];

    // Don't show manual payment options for downloadables
    if ((isset($contain_digital_carts) && $contain_digital_carts) || $cart->is_digital) {
        $paymentMethods = $paymentMethods->reject(function ($paymentMethod) {
            return in_array($paymentMethod->code, ['cod', 'wire']);
        });
    }
  @endphp

  @foreach ($paymentMethods as $paymentMethod)
    {{-- When admin get paid but still give option to vendors on/off a active payment method --}}
    @continue(!vendor_get_paid_directly() && isset($shop) && vendor_can_on_off_payment_method() && !in_array($paymentMethod->id, $active_payment_methods))
    @php
      $config = get_payment_config_info($paymentMethod->code, $config_shop);

      $suffix = '';
      if ($paymentMethod->code == 'zcart-wallet' && isset($config['config'])) {
          $suffix = ' (' . get_formated_currency($config['config']) . ')';
      }

      // Share the payment config to global level so other views can access
      \View::share('config_' . str_replace('-', '_', $paymentMethod->code), json_encode($config));
    @endphp

    {{-- Skip the payment option if not confirured --}}
    @continue(!$config || !is_array($config) || !$config['config'])

    {{-- @if ($paymentMethod->code !== 'pip' && $shop->config->pay_online) --}}
    @if ($customer && $paymentMethod->code == 'stripe' && $customer->hasBillingToken())
      <div class="form-group">
        <label>
          <input name="payment_method" value="saved_card" class="i-radio-blue payment-option" type="radio" data-info="{{ $config['msg'] }}" data-type="{{ $paymentMethod->type }}" required="required" {{ old('payment_method') ? '' : 'checked' }} /> @lang('theme.card'): <i class="fab fa-cc-{{ strtolower($customer->pm_type) }}"></i> ************{{ $customer->pm_last_four }}
        </label>
      </div>
      {{-- @endif --}}
    @endif

    {{-- @if ($paymentMethod->code == 'pip' && $shop->config->pay_in_person) --}}
    <div class="form-group">
      <label>
        <input name="payment_method" value="{{ $paymentMethod->code }}" data-code="{{ $paymentMethod->code }}" class="i-radio-blue payment-option" type="radio" data-info="{{ $config['msg'] }}" data-type="{{ $paymentMethod->type }}" required="required" {{ old('payment_method') == $paymentMethod->code ? 'checked' : '' }} />

        <span>
          {{ $paymentMethod->code == 'stripe' ? trans('theme.credit_card') : $paymentMethod->name . $suffix }}
        </span>
      </label>
    </div>
    {{-- @endif --}}
  @endforeach
</div>

{{-- authorize-net --}}
@include('partials.authorizenet_card_form')

{{-- Stripe --}}
@include('partials.stripe_card_form')

{{-- Razorpay --}}
@if (is_incevio_package_loaded('razorpay'))
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endif

{{-- M-Pesa Payment --}}
@if (is_incevio_package_loaded('mpesa'))
  @include('mpesa::mpesa_payment_form')
@endif

{{-- Warehouse adddress --}}
<div id="payInPerson" class="hide">
  <h3 class="widget-title">{{ trans('theme.pickup') }}</h3>
  @php
    $warehouseIds = [];
  @endphp

  @foreach ($cart->inventories as $key => $inventory)
    @if ($inventory->warehouse)
      @if (!in_array($inventory->warehouse_id, $warehouseIds))
        @php
          $warehouseIds[] = $inventory->warehouse_id;
        @endphp

        <ul class="shopping-cart-summary">
          <li class="text-left">
            <span>{{ trans('theme.notify.business_days') }}</span>
            <span></span>
          </li>
          <li class="text-left">
            @foreach ($inventory->warehouse->business_days as $buseiness_day)
              <span class="badge badge-dark">{{ $buseiness_day }}</span>
            @endforeach
          </li>
          <li>
            {{ trans('theme.pickup_time') . ': ' . $inventory->warehouse->opening_time . '-' . $inventory->warehouse->close_time }}
          </li>
          <li>
            {!! trans('theme.address') . ': ' . $inventory->warehouse->address->toHtml() !!}
          </li>
        </ul>
      @endif
    @endif
  @endforeach
</div>
{{-- End warreHouse address --}}

<p id="payment-instructions" class="text-primary my-3">
  <i class="fa fa-info-circle"></i>
  <span>@lang('theme.placeholder.select_payment_option')</span>
</p>

<div class="form-group mb-5">
  <div class="checkbox">
    <label>
      {!! Form::checkbox('agree', null, null, ['class' => 'i-check-blue', 'required']) !!} {!! trans('theme.input_label.i_agree_with_terms', ['url' => route('page.open', \App\Models\Page::PAGE_TNC_FOR_CUSTOMER)]) !!}
    </label>
  </div>
  <div class="help-block with-errors"></div>
</div>

<div id="submit-btn-block" class="clearfix mb-3 {{ $cart->is_digital ? 'digital_checkout_pay' : '' }}" style="display: none;">
  <button id="pay-now-btn" class="btn btn-primary btn-lg btn-block py-3" type="submit">
    <small>
      <i class="far fa-shield"></i> <span id="pay-now-btn-txt">@lang('theme.button.checkout')</span>
    </small>
  </button>

  <a href="javascript:void(0)" id="paypal-express-btn" class="hide" type="submit">
    <img src="{{ asset(sys_image_path('payment-methods') . 'paypal-express.png') }}" width="70%" alt="paypal express checkout" title="paypal-express" />
  </a>
</div>

{{-- MercadoPago Payment --}}
@if (is_incevio_package_loaded('mercado-pago'))
  <script src="https://sdk.mercadopago.com/js/v2"></script>

  @include('mercadoPago::card_form')
@endif
