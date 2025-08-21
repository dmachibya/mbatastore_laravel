@extends('auth.master')

@section('content')
  @if (is_incevio_package_loaded('otp-login'))
    @include('otp-login::admin_login')
  @else
    <div class="box">
      <div class="login-section">
        <div class="form-container">
          <div class="image-holder"></div>
          <div class="login-form-section">
            <div class="login-logo">
              <a href="{{ url('/') }}">
                <img src="{{ get_logo_url('system', 'logo') }}" class="brand-logo" height="47px" alt="{{ trans('theme.logo') }}" title="{{ trans('theme.logo') }}">
              </a>
            </div>

            <div class="form-section">
              <h3 class="text-center mt-0">{{ trans('app.login') }}</h3>

              {!! Form::open(['route' => 'login', 'id' => 'form', 'data-toggle' => 'validator']) !!}
              <div class="form-group has-feedback">
                {!! Form::email('email', null, ['id' => 'email', 'class' => 'form-control input-lg', 'placeholder' => trans('app.form.email_address'), 'required']) !!}
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                <div class="help-block with-errors"></div>
              </div>

              <div class="form-group has-feedback">
                {!! Form::password('password', ['id' => 'password', 'class' => 'form-control input-lg', 'id' => 'password', 'placeholder' => trans('app.form.password'), 'data-minlength' => '6', 'required']) !!}
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                <div class="help-block with-errors"></div>
              </div>

              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>
                      {!! Form::checkbox('remember', null, null, ['class' => 'icheck']) !!} {{ trans('app.form.remember_me') }}
                    </label>
                  </div>
                </div> <!-- /.col-* -->

                <div class="col-sm-6">
                  @unless (is_incevio_package_loaded('otp-login'))
                    <a class="btn btn-link pull-right nopadding-right" href="{{ route('password.request') }}">{{ trans('app.form.forgot_password') }}</a>
                  @endunless
                </div> <!-- /.col-* -->
              </div> <!-- /.row -->

              {!! Form::submit(trans('app.form.login'), ['class' => 'btn btn-block btn-lg btn-flat btn-primary']) !!}

              {!! Form::close() !!}

              <div class="spacer20"></div>

              <a class="btn btn-link nopadding-left" href="{{ route('vendor.register') }}" class="text-center">
                <i class="fa fa-laptop" aria-hidden="true"></i> {{ customer_can_register() ? trans('app.form.register_as_merchant') : trans('app.form.register') }}
              </a>
            </div> <!-- /.form-section -->
            @include('partials._demo_admin_login')
          </div> <!-- /.login-form-section -->
        </div> <!-- /.form-container -->
      </div> <!-- /.login-section -->
    </div> <!-- /.box -->
  @endif
@endsection
