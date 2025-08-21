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

          <div class="form-section customer-login">
            <h3 class="text-center mt-0">{{ trans('theme.account_login') }}</h3>
            {!! Form::open(['route' => 'customer.login.submit', 'id' => 'loginForm-1', 'data-toggle' => 'validator']) !!}
            <div class="form-group has-feedback">
              {!! Form::email('email', null, ['id' => 'email', 'class' => 'form-control input-lg', 'placeholder' => trans('theme.placeholder.email'), 'required']) !!}
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback">
              {!! Form::password('password', ['id' => 'password', 'class' => 'form-control input-lg', 'placeholder' => trans('theme.placeholder.password'), 'data-minlength' => '6', 'required']) !!}
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="row">
              <div class="col-sm-7">
                <div class="form-group">
                  <label>
                    {!! Form::checkbox('remember', null, null, ['class' => 'icheck']) !!} {{ trans('theme.remember_me') }}
                  </label>
                </div>
              </div>

              <div class="col-sm-5 pull-right">
                {!! Form::submit(trans('theme.button.login'), ['class' => 'btn btn-block btn-lg btn-flat btn-primary']) !!}
              </div>
            </div>
            {!! Form::close() !!}

            <a class="btn btn-link" href="{{ route('customer.password.request') }}">
              {{ trans('theme.forgot_password') }}
            </a>

            <a class="btn btn-link" href="{{ route('customer.register') }}" class="text-center">
              {{ trans('theme.register_here') }}
            </a>

            @include('theme::auth._social_login')
          </div>
          @include('partials._demo_customer_login')
        </div> <!-- /.login-form-section -->
      </div> <!-- /.form-container -->
    </div> <!-- /.login-section -->
  </div> <!-- /.box -->
@endsection
