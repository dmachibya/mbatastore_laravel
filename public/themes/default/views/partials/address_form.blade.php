<div class="form-group textClass" id="address_title_container">
  <div class="input-group">
    <span class="input-group-addon flat"><i class="fas fa-user"></i></span>
    {!! Form::text('address_title', null, ['id' => 'address_title', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.address_title') . '*', 'pattern' => '^[A-Za-z]+\s[A-Za-z]+$', 'data-error' => trans('app.full_name_required'), 'required']) !!}
  </div>
  <div class="help-block with-errors"></div>
</div>

<div class="form-group textClass" id="address_line_1_container">
  {!! Form::text('address_line_1', null, ['id' => 'address_line_1', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.address_line_1') . '*', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group textClass" id="address_line_2_container">
  {!! Form::text('address_line_2', null, ['id' => 'address_line_2', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.address_line_2')]) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="row textClass" id="country_id_container">
  <div class="col-xl-8">
    <div class="form-group">
      @if (is_address_autocomplete_on())
        {!! Form::text('country_id', isset($cart) ? $cart->country->name : null, ['id' => 'address_country', 'class' => 'form-control', 'placeholder' => trans('theme.country') . '*', 'required']) !!}
        <div class="help-block with-errors"></div>
      @elseif (isset($one_checkout_form))
        {!! Form::text('country_id', $cart->country->name, ['id' => 'address_country', 'class' => 'form-control', 'disabled' => 'true']) !!}
        <div class="help-block with-errors small text-warning">{{ trans('packages.checkout.make_changes_on_cart_page') }}</div>
      @else
        {!! Form::select('country_id', $countries, isset($address) ? null : (isset($cart) ? $cart->ship_to_country_id : config('system_settings.address_default_country')), ['id' => 'address_country', 'class' => 'form-control flat', 'placeholder' => trans('theme.country') . '*', 'required']) !!}
        <div class="help-block with-errors"></div>
      @endif
    </div>
  </div>
  <div class="col-xl-4 pl-xl-0">
    <div class="form-group">
      {!! Form::text('zip_code', null, ['id' => 'postcode', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.zip_code') . '*', 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

<div class="row textClass" id="address_city_container">
  <div class="col-xl-6 pr-xl-0">
    <div class="form-group">
      {!! Form::text('city', null, ['id' => 'address_city', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.city') . '*', 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>

  <div class="col-xl-6">
    <div class="form-group">
      @if (is_address_autocomplete_on())
        {!! Form::text('state_id', isset($cart) ? $cart->state->name : null, ['id' => 'address_state', 'class' => 'form-control', 'placeholder' => trans('theme.placeholder.state') . '*', 'required']) !!}
        <div class="help-block with-errors"></div>
      @elseif (isset($one_checkout_form))
        {!! Form::text('state_id', $cart->state->name, ['id' => 'address_state', 'class' => 'form-control', 'disabled' => 'true']) !!}
        <div class="help-block with-errors small text-warning">{{ trans('packages.checkout.make_changes_on_cart_page') }}</div>
      @else
        {!! Form::select('state_id', isset($states) ? $states : [], isset($cart) ? $cart->ship_to_state_id : (isset($address) ? null : config('system_settings.address_default_state')), ['id' => 'address_state', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.state') . '*', empty($states) ? '' : 'required']) !!}
        <div class="help-block with-errors"></div>
      @endif
    </div>
  </div>
</div>

<div class="form-group textClass">
  {!! Form::text('phone', null, ['id' => 'address_phone', 'class' => 'form-control flat', 'placeholder' => trans('theme.placeholder.phone_number')]) !!}
  <div class="help-block with-errors"></div>
</div>

{{-- GOOGLE PLACE API --}}
@if (is_address_autocomplete_on())
  @include('scripts.google_place')
@endif
