@extends('theme::auth.layout')

@section('content')
  <div class="box">
    <div class="login-section">
      <div class="form-container">
        <div class="image-holder-customer"></div>
        <div class="login-form-section">
          <div class="login-logo">
            <a href="{{ url('/') }}">
              <img src="{{ get_logo_url('system', 'logo') }}" class="brand-logo" height="47px" alt="{{ trans('theme.logo') }}" title="{{ trans('theme.logo') }}">
            </a>
          </div>

          <div class="form-section">
            <h3 class="text-center mt-0">{{ trans('theme.register') }}</h3>
            {!! Form::open(['route' => 'customer.register', 'id' => 'form', 'data-toggle' => 'validator', 'files' => true]) !!}
            <div class="form-group has-feedback">
              {!! Form::text('name', null, ['class' => 'form-control input-lg', 'placeholder' => trans('theme.placeholder.full_name'), 'required']) !!}
              <span class="glyphicon glyphicon-user form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback">
              {!! Form::email('email', null, ['class' => 'form-control input-lg', 'placeholder' => trans('theme.placeholder.valid_email'), 'required']) !!}
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            @if (is_incevio_package_loaded('otp-login'))
              @include('otp-login::phone_field')
            @endif

            <div class="form-group has-feedback">
              {!! Form::password('password', ['class' => 'form-control input-lg', 'id' => 'password', 'placeholder' => trans('theme.placeholder.password'), 'data-minlength' => '6', 'required']) !!}
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback">
              {!! Form::password('password_confirmation', ['class' => 'form-control input-lg', 'placeholder' => trans('theme.placeholder.confirm_password'), 'data-match' => '#password', 'required']) !!}
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            @if (is_incevio_package_loaded('zipcode'))
              @include('address._form')
            @endif

            @if (is_incevio_package_loaded('smartForm'))
              @include('smartForm::partials.customer_register_page_form_fields')
            @endif

            @if (is_incevio_package_loaded('buyerGroup'))
              @include('buyerGroup::registration_form_fields')
            @elseif(is_incevio_package_loaded('smartForm'))
              @include('smartForm::partials._parsed_input_fields', ['row' => smart_form_fields(config('system_settings.smart_form_id_for_customer_registration_form'))])
            @endif

            <div class="form-group has-feedback">
              @if (config('services.recaptcha.key'))
                <div class="g-recaptcha" data-sitekey="{!! config('services.recaptcha.key') !!}"></div>
              @endif
              <div class="help-block with-errors"></div>
            </div>

            @if (config('system_settings.ask_customer_for_email_subscription'))
              <div class="form-group">
                <label>
                  {!! Form::checkbox('subscribe', null, null, ['class' => 'icheck']) !!} {!! trans('theme.input_label.subscribe_to_the_newsletter') !!}
                </label>
              </div>
            @endif
            
            <div class="row">
              @if (config('system_settings.show_customer_terms_and_conditions'))
                <div class="col-sm-7">
                  <div class="form-group">
                    <label>
                      {!! Form::checkbox('agree', null, null, ['class' => 'icheck', 'required']) !!} {!! trans('theme.input_label.i_agree_with_terms', ['url' => route('page.open', \App\Models\Page::PAGE_TNC_FOR_CUSTOMER)]) !!}
                    </label>
                    <div class="help-block with-errors"></div>
                  </div>
                </div>
              @endif
              <div class="col-sm-5">
                {!! Form::submit(trans('theme.register'), ['class' => 'btn btn-block btn-lg btn-flat btn-primary']) !!}
              </div>
            </div>
            {!! Form::close() !!}
            <a href="{{ route('customer.login') }}" class="btn btn-link">{{ trans('theme.have_an_account') }}</a>

            @include('theme::auth._social_login')
          </div> <!-- /.form-section -->
        </div> <!-- /.login-form-section -->
      </div> <!-- /.form-container -->
    </div> <!-- /.login-section -->
  </div> <!-- /.box -->
@endsection
