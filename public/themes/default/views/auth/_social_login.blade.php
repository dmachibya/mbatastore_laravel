@if (config('system_settings.social_auth'))
  <div class="social-auth-links text-center">
    <a href="{{ route('socialite.customer', 'facebook') }}" class="demo-restrict btn btn-social btn-facebook btn-lg btn-flat">
      <i class="fa fa-facebook"></i> {{ trans('theme.button.facebook') }}
    </a>

    <a href="{{ route('socialite.customer', 'google') }}" class="demo-restrict btn btn-social btn-google btn-lg btn-flat">
      <i class="fa fa-google"></i> {{ trans('theme.button.google') }}
    </a>

    @if (is_incevio_package_loaded('apple-login'))
      <a href="{{ route('socialite.customer', 'apple') }}" class="demo-restrict btn btn-social btn-apple btn-lg btn-flat">
        <i class="fa fa-apple"></i> {{ trans('packages.apple-login.apple') }}
      </a>
    @endif
  </div> <!-- /.social-auth-links -->
@endif
