@extends('auth.master')

@section('content')
  @if (session('status'))
    <div class="alert alert-success">
      {{ session('status') }}
    </div>
  @endif

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
            <h3 class="text-center mt-0">{{ trans('app.form.password_reset') }}</h3>

            {!! Form::open(['route' => 'password.email', 'id' => 'form', 'data-toggle' => 'validator']) !!}
            <div class="form-group has-feedback">
              {!! Form::email('email', null, ['class' => 'form-control input-lg', 'placeholder' => trans('app.placeholder.valid_email'), 'required']) !!}
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
              <div class="help-block with-errors"></div>
            </div>

            {!! Form::submit(trans('app.form.send_password_link'), ['class' => 'btn btn-lg btn-flat btn-primary']) !!}
            {!! Form::close() !!}

            <div class="spacer20"></div>

            <a href="{{ route('login') }}" class="btn btn-link">{{ trans('app.login') }}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
