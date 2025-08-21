@extends('auth.master')

@section('content')
  <div class="box">
    <div class="login-section">
      <div class="form-container">
        <div class="image-holder"></div>
        <div class="login-form-section">
          <div class="login-logo">
            <a href="{{ url('/') }}">
              <img src="{{ get_logo_url('system', 'full') }}" class="brand-logo" height="47px" alt="{{ trans('theme.logo') }}" title="{{ trans('theme.logo') }}">
            </a>
          </div>
          <div class="form-section">
            <h3 class="text-center mt-0">{{ trans('app.form.register') }}</h3>
            {!! Form::open(['route' => 'register', 'id' => config('system_settings.required_card_upfront') ? 'stripe-form' : 'registration-form', 'data-toggle' => 'validator', 'files' => true]) !!}

            @if (is_subscription_enabled())
              <div class="form-group has-feedback">
                {{ Form::select('plan', $plans, isset($plan) ? $plan : null, ['id' => 'plans', 'class' => 'form-control input-lg', 'required']) }}
                <i class="glyphicon glyphicon-dashboard form-control-feedback"></i>
                <div class="help-block with-errors">
                  @if ((bool) config('system_settings.trial_days'))
                    {{ trans('help.charge_after_trial_days', ['days' => config('system_settings.trial_days')]) }}
                  @endif
                </div>
              </div>
            @endif

            <div class="form-group has-feedback">
              {!! Form::text('name', null, ['class' => 'form-control input-lg', 'placeholder' => trans('theme.placeholder.full_name'), 'required']) !!}
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback">
              {!! Form::email('email', null, ['class' => 'form-control input-lg', 'placeholder' => trans('app.placeholder.valid_email'), 'required']) !!}
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback">
              {!! Form::password('password', ['class' => 'form-control input-lg', 'id' => 'password', 'placeholder' => trans('app.placeholder.password'), 'data-minlength' => '6', 'required']) !!}
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback">
              {!! Form::password('password_confirmation', ['class' => 'form-control input-lg', 'placeholder' => trans('app.placeholder.confirm_password'), 'data-match' => '#password', 'required']) !!}
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            @if (is_incevio_package_loaded('otp-login'))
              @include('otp-login::phone_field')
            @else
              <div class="form-group has-feedback">
                {!! Form::text('phone', null, ['class' => 'form-control input-lg', 'placeholder' => trans('app.placeholder.phone')]) !!}
                <i class="glyphicon glyphicon-phone form-control-feedback"></i>
                <div class="help-block with-errors"></div>
              </div>
            @endif

            <div class="form-group has-feedback">
              {!! Form::text('shop_name', null, ['class' => 'form-control input-lg', 'placeholder' => trans('app.placeholder.shop_name'), 'required']) !!}
              <i class="glyphicon glyphicon-equalizer form-control-feedback"></i>
              <div class="help-block with-errors"></div>
            </div>

            @if (\App\Models\SystemConfig::vendorRegistrationHasAdditionalFields())
              @include('smartForm::partials._parsed_input_fields', ['row' => smart_form_fields(config('system_settings.smart_form_id_for_vendor_additional_info'))])
            @endif

            @if (config('services.recaptcha.key'))
              <div class="form-group has-feedback">
                <div class="g-recaptcha" data-sitekey="{!! config('services.recaptcha.key') !!}"></div>
                <div class="help-block with-errors"></div>
              </div>
            @endif
            @if (config('system_settings.show_vendor_terms_and_conditions'))
              <div class="form-group">
                <label>
                  {!! Form::checkbox('agree', null, null, ['class' => 'icheck', 'required']) !!} {!! trans('app.form.i_agree_with_merchant_terms', ['url' => route('page.open', \App\Models\Page::PAGE_TNC_FOR_MERCHANT)]) !!}
                </label>
                <div class="help-block with-errors"></div>
              </div>
            @endif
            {!! Form::submit(trans('app.form.register'), ['id' => 'card-button', 'class' => 'btn btn-block btn-lg btn-flat btn-primary']) !!}

            {!! Form::close() !!}

            <div class="spacer10"></div>

            <a href="{{ route('login') }}" class="btn btn-link nopadding-left">
              <i class="fa fa-user-o" aria-hidden="true"></i> {{ trans('app.form.have_an_account') }} | <i class="fa fa-sign-in" aria-hidden="true"></i> {{ trans('app.login') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{--  recaptcha api --}}
  <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection
