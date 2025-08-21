@php
  $geoip = geoip(get_visitor_IP());
  $geoip_country = $business_areas->where('iso_code', $geoip->iso_code)->first();

  $shipping_country_id = $cart->ship_to_country_id ?? optional($geoip_country)->id;

  if (!$cart->shipping_state_id) {
      $geoip_state = \DB::table('states')
          ->select('id', 'name', 'iso_code')
          ->where([['country_id', '=', $shipping_country_id], ['iso_code', '=', $geoip->state]])
          ->first();
  }

  $shipping_state_id = $cart->ship_to_state_id ?? optional($geoip_state)->id;

  $dec = is_non_decimal_currency() ? 0 : config('system_settings.decimals', 2);

  $packaging_options = null;
  if (!$cart->is_digital && is_incevio_package_loaded('packaging')) {
      $packaging_options = optional($cart->shop)->packagings;

      $default_packaging =
          $cart->shippingPackage ??
          (optional($cart->shop->packagings)
              ->where('default', 1)
              ->first() ??
              $platformDefaultPackaging);
  }
@endphp

<section class="checkout-area mt-3 mt-lg-0">
  <div class="container">
    @if (Session::has('error'))
      <div class="notice notice-danger notice-sm">
        <strong>{{ trans('theme.error') }}</strong> {{ Session::get('error') }}
      </div>
    @endif

    <div class="notice notice-warning notice-sm mb-3" id="checkout-notice" style="display: {{ $cart->is_digital || $cart->shipping_rate_id || $cart->is_free_shipping() ? 'none' : 'block' }};">
      <strong>{{ trans('theme.warning') }}</strong>
      <span id="checkout-notice-msg">@lang('theme.notify.seller_doesnt_ship')</span>
    </div>

    {!! Form::open(['route' => ['order.create', $cart], 'id' => 'formId' . $cart->id, 'name' => 'checkoutForm', 'files' => true, 'data-toggle' => 'validator', 'autocomplete' => 'off', 'novalidate']) !!}
    <div class="row justify-content-center shopping-cart-wrapper radius mb-4" id="cartId{{ $cart->id }}" data-cart="{{ $cart->id }}" data-cart-type="{{ $cart->is_digital ? 'digital' : 'physical' }}">
      <div class="col-lg-4 bg-light">
        <div class="seller-info my-3">
          <div class="text-muted small mb-3">
            <i class="far fa-store"></i> {{ trans('theme.sold_by') }}
          </div>

          <div class="logo-wrapper flex-between-center">
            <img class="lazy vendor-logo" src="{{ get_storage_file_url(optional($shop->logoImage)->path, 'tiny_thumb') }}" data-src="{{ get_storage_file_url(optional($shop->logoImage)->path, 'medium') }}" alt="{{ $shop->name }}">

            <a href="{{ route('show.store', $shop->slug) }}" class="seller-info-name ml-2">
              <img>
              {!! $shop->getQualifiedName() !!}
            </a>

            <div>
              {!! $shop->reward_badge !!}
            </div>
          </div> <!-- /.logo-wrapper -->
        </div><!-- /.seller-info -->

        <div class="input-group w-100 radius mb-4">
          <span class="input-group-addon">
            <i class="fas fa-ticket no-fill"></i>
          </span>

          <input name="coupon" value="{{ $cart->coupon ? $cart->coupon->code : null }}" id="coupon{{ $cart->id }}" class="form-control" type="text" placeholder="@lang('theme.placeholder.have_coupon_from_seller')">

          <span class="input-group-btn">
            <button class="btn btn-default apply_seller_coupon" type="button" data-cart="{{ $cart->id }}">@lang('theme.button.apply_coupon')</button>
          </span>
        </div><!-- /input-group -->

        {{ Form::hidden('cart_id', $cart->id, ['id' => 'checkout-id']) }}
        {{ Form::hidden('cart_weight', $cart->shipping_weight, ['id' => 'cartWeight' . $cart->id]) }}
        {{ Form::hidden('free_shipping', $cart->is_free_shipping(), ['id' => 'freeShipping' . $cart->id]) }}
        {{ Form::hidden('shop_id', $cart->shop->id, ['id' => 'shop-id' . $cart->id]) }}
        {{ Form::hidden('tax_id', isset($shipping_zones[$cart->id]->i) ? $shipping_zones[$cart->id]->tax_id : null, ['id' => 'tax-id' . $cart->id]) }}
        {{ Form::hidden('taxrate', $cart->taxrate, ['id' => 'cart-taxrate' . $cart->id]) }}

        @if (!$cart->is_digital && is_incevio_package_loaded('packaging'))
          {{ Form::hidden('packaging_id', $cart->packaging_id ?? optional($default_packaging)->id, ['id' => 'packaging-id' . $cart->id]) }}
        @endif

        {{ Form::hidden('shipping_zone_id', $cart->shipping_zone_id, ['id' => 'zone-id' . $cart->id]) }}
        {{ Form::hidden('shipping_rate_id', $cart->shipping_rate_id, ['id' => 'shipping-rate-id' . $cart->id]) }}
        {{ Form::hidden('ship_to_country_id', $cart->ship_to_country_id, ['id' => 'shipto-country-id' . $cart->id]) }}
        {{ Form::hidden('ship_to_state_id', $cart->ship_to_state_id, ['id' => 'shipto-state-id' . $cart->id]) }}
        {{ Form::hidden('coupon_raw', json_encode($cart->coupon), ['id' => 'coupon-raw' . $cart->id]) }}
        {{ Form::hidden('handling_cost', $cart->handling_cost > 0 ? get_formated_price_value($cart->handling_cost) : getHandelingCostOf($cart->shop_id), ['id' => 'handling-cost' . $cart->id]) }}

        <h3 class="widget-title">{{ trans('theme.order_info') }}</h3>
        <ul class="shopping-cart-summary">
          <li>
            <span>{{ trans('theme.item_count') }}</span>
            <span>{{ $cart->inventories_count }}</span>
          </li>

          @if (!$cart->is_digital)
            <li>
              <span>
                {{ trans('theme.quantity') }}
              </span>
              <span>{{ $cart->quantity }}</span>
            </li>
          @endif

          <li>
            <span>{{ trans('theme.subtotal') }}</span>
            <span>{{ get_currency_prefix() }}
              <span id="summary-total{{ $cart->id }}" class="item-total{{ $cart->id }}">{{ number_format($cart->total, $dec, '.', '') }}</span>{{ get_currency_suffix() }}
            </span>
          </li>

          @unless ($cart->is_digital)
            <li>
              <span>
                <a class="dynamic-shipping-rates" data-toggle="popover" data-cart="{{ $cart->id }}" data-options="{{ $shipping_options[$cart->id] }}" id="shipping-options{{ $cart->id }}" title="{{ trans('theme.shipping') }}">
                  <u>{{ trans('theme.shipping') }}</u>
                </a>
                <em id="summary-shipping-name{{ $cart->id }}" class="small text-muted"></em>
              </span>

              <span>{{ get_currency_prefix() }}
                <span id="summary-shipping{{ $cart->id }}">{{ number_format($cart->get_shipping_cost(), $dec, '.', '') }}</span>{{ get_currency_suffix() }}
              </span>
            </li>

            @if (is_incevio_package_loaded('packaging'))
              @unless (empty(json_decode($packaging_options)))
                <li>
                  <span>
                    <a class="packaging-options" data-toggle="popover" data-cart="{{ $cart->id }}" data-options="{{ $packaging_options }}" title="{{ trans('theme.packaging') }}">
                      <u>{{ trans('theme.packaging') }}</u>
                    </a>

                    <em class="small text-muted" id="summary-packaging-name{{ $cart->id }}">
                      {{ optional($default_packaging)->name }}
                    </em>
                  </span>

                  <span>{{ get_currency_prefix() }}
                    <span id="summary-packaging{{ $cart->id }}">
                      {{ number_format($default_packaging ? get_formated_price_value($default_packaging->cost) : 0, $dec, '.', '') }}
                    </span>{{ get_currency_suffix() }}
                  </span>
                </li>
              @endunless
            @endif
          @endunless

          <li id="discount-section-li{{ $cart->id }}" style="display: {{ $cart->discount > 0 ? 'block' : 'none' }};">
            <span>{{ trans('theme.discount') }}
              <em id="summary-discount-name{{ $cart->id }}" class="small text-muted">{{ $cart->coupon ? $cart->coupon->name . ' (' . $cart->coupon->getFormatedAmountText() . ')' : '' }}</em>
            </span>

            <span>-{{ get_currency_prefix() }}
              <span id="summary-discount{{ $cart->id }}">{{ $cart->coupon ? number_format($cart->discount, $dec, '.', '') : number_format(0, $dec, '.', '') }}</span>{{ get_currency_suffix() }}
            </span>
          </li>

          <li id="tax-section-li{{ $cart->id }}" style="display: {{ $cart->taxes > 0 ? 'block' : 'none' }};">
            <span>{{ trans('theme.taxes') }}</span>

            <span>{{ get_currency_prefix() }}
              <span id="summary-taxes{{ $cart->id }}">{{ number_format($cart->taxes, $dec, '.', '') }}</span>{{ get_currency_suffix() }}
            </span>
          </li>

          <li>
            <span>{{ trans('theme.total') }}</span>

            <span>{{ get_currency_prefix() }}
              <span id="summary-grand-total{{ $cart->id }}">{{ number_format($cart->calculate_grand_total(), $dec, '.', '') }}</span>{{ get_currency_suffix() }}
            </span>
          </li>
        </ul>

        @if ($trust_badge = get_trust_badge_url())
          <div class="text-center my-4">
            <img src="{{ $trust_badge }}" alt="{{ trans('theme.trust_badge') }}"/>
          </div>
        @endif

        <div class="text-center mb-3 d-flex justify-content-around checkout-btns">
          <a class="btn btn-primary py-2 px-4" href="{{ route('cart.index') }}">
            {{ trans('theme.button.update_cart') }}
          </a>

          <a class="btn btn-primary py-2 px-4" href="{{ url('/') }}">
            {{ trans('theme.button.continue_shopping') }}
          </a>
        </div>
      </div> <!-- /.col-md-3 -->

      <div class="col-lg-5 col-md-6 py-2 px-4 border-r">
        @if ($cart->is_digital)
          <h3 class="widget-title">
            {{ trans('theme.billing_address') }}:
          </h3>
        @else
          <div class="widget-title d-flex justify-content-between">
            <div class="col-6 p-0 justify-content-between">
              <input class="form-check-input" type="radio" name="fulfilment_type" id="fulfilment_type_deliver" value="{{ \App\Models\Order::FULFILMENT_TYPE_DELIVER }}" checked>
              <label for="fulfilment_type_deliver">
                <i class="far fa-shipping-fast"></i>
                <span id="checkoutpage_shipping_form_title">{{ trans('theme.ship_to') }}</span>
              </label>
            </div>

            @if ($shop->isPickupEnabled())
              <!-- Pickup option shows only when shop has a warehouse and pickup is enabled by admin -->
              <div class="col-6 p-0 justify-content-between">
                <input class="form-check-input" type="radio" name="fulfilment_type" id="fulfilment_type_pickup" value="{{ \App\Models\Order::FULFILMENT_TYPE_PICKUP }}">
                <label for="fulfilment_type_pickup">
                  <i class="far fa-shopping-basket"></i>
                  <span id="checkoutpage_shipping_form_title">{{ trans('theme.pickup_from') }}</span>
                </label>
              </div>
            @endif
          </div>
        @endif

        <div class="form-group mb-4 hidden" id="pickup_details">
          <div class="row warehouse-address-list">
            @forelse ($shop->warehouses as $warehouse)
              <div class="col-sm-12 col-md-6 p-0-{{ $loop->iteration % 2 == 1 ? 'right' : 'left' }} textClass">
                <div class="address-list-item">
                  <i class="fa fa-home"></i><strong> {!! $warehouse->name !!} </strong><br>
                  <i class="fa fa-map-marker"></i> <em>{{ trans('app.address') }} :</em>
                  {!! $warehouse->address->toHtml(', ', false) !!}

                  <p><em>{{ trans('theme.pickup_time') }} :</em></p>

                  @if (is_array($warehouse->business_days))
                    <i class="fa fa-calendar"></i> {{ implode(', ', $warehouse->business_days) }}<br />
                  @endif

                  @if ($warehouse->opening_time && $warehouse->close_time)
                    <i class="fa fa-clock-o"></i> {{ $warehouse->opening_time }} - {{ $warehouse->close_time }}
                  @endif

                  <input type="radio" class="warehouse_id" name="warehouse_id" value="{{ $warehouse->id }}">
                </div>
              </div>
              @if ($loop->iteration % 2 == 0)
                <div class="clearfix"></div>
              @endif
            @empty
              <div class="col-sm-12">
                <h4 class="my-3 text-info">{{ trans('theme.no_pickup_options') }}</h4>
              </div>
            @endforelse
          </div>
          <hr class="dotted" />
        </div>

        @if (isset($customer))
          {{ trans('theme.customer_address') }}

          <div class="row customer-address-list">
            @php
              $pre_select = null;
            @endphp

            @foreach ($customer->addresses as $address)
              @php
                $ship_to_this_address = null;

                // If any address not selected yet
                if ($pre_select == null) {
                    if ($customer->addresses->count() == 1) {
                        // Has onely address
                        $pre_select = 1;
                        $ship_to_this_address = true;
                    } elseif (Request::has('address')) {
                        // Just created this address
                        if (Request::get('address') == $address->id) {
                            $pre_select = 1;
                            $ship_to_this_address = true;
                        }
                    } elseif ($cart->ship_to_country_id == $address->country_id && $cart->ship_to_state_id == $address->state_id) {
                        // Zone selected at cart page
                        $pre_select = 1;
                        $ship_to_this_address = true;
                    } elseif ($cart->ship_to == null && $address->address_type === 'Shipping') {
                        // Customer's shipping address
                        $pre_select = 1;
                        $ship_to_this_address = true;
                    }
                }
              @endphp

              <div class="col-sm-12 col-md-6 p-0-{{ $loop->iteration % 2 == 1 ? 'right' : 'left' }} textClass">
                <div class="address-list-item {{ $ship_to_this_address == true ? 'selected' : '' }}">
                  {!! $address->toHtml('<br/>', false) !!}
                  <input type="radio" class="ship-to-address" name="ship_to" value="{{ $address->id }}" {{ $ship_to_this_address == true ? 'checked' : '' }} data-country="{{ $address->country_id }}" data-state="{{ $address->state_id }}" required>
                </div>
              </div>

              @if ($loop->iteration % 2 == 0)
                <div class="clearfix"></div>
              @endif
            @endforeach
          </div>

          <small id="ship-to-error-block" class="text-danger pull-right"></small>

          <div class="col-sm-12 my-3 addNewAddrs">
            <a href="{{ route('my.address.create') }}" class="modalAction btn btn-default btn-sm pull-right">
              <i class="fas fa-address-card-o"></i> @lang('theme.button.add_new_address')
            </a>
          </div>
        @else
          <div class="checkout-shiping-address">
            @include('theme::partials.checkout_shiping_address')
          </div>

          @if ($cart->has_credit_rewards())
            <span class="text-dark">
              <i class="fa fa-warning"></i>
              {{ trans('packages.wallet.create_an_account_to_get_reward') }}
            </span>
          @endif
        @endif

        <hr class="dotted" />

        @if (is_incevio_package_loaded('pharmacy'))
          @include('pharmacy::checkout_form')
        @endif

        <div class="form-group">
          {!! Form::label('buyer_note', trans('theme.leave_message_to_seller'), ['class' => 'buyer_note']) !!}
          {!! Form::textarea('buyer_note', null, ['class' => 'form-control summernote-without-toolbar', 'placeholder' => trans('theme.placeholder.message_to_seller'), 'rows' => '3', 'maxlength' => '250']) !!}
          <div class="help-block with-errors"></div>
        </div>
      </div> <!-- /.col-md-5 -->

      <div class="col-lg-3 col-md-6 cart-payment-options py-2">
        @include('partials.payment_options')
      </div> <!-- /.col-md-4 -->
    </div><!-- /.row -->
    {!! Form::close() !!}
  </div>
</section>

@if (config('services.google.gtm_container_id'))
  @include('scripts.dataLayer.checkout_page')
@endif
