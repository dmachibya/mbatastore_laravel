@if (config('system_settings.social_auth'))
  <div class="text-center p-3">{{ trans('theme.login_with_social') }}</div>
  <div class="d-flex justify-content-center social-buttons">
    <a href="{{ route('socialite.customer', 'facebook') }}" class="demo-restrict btn btn-facebook btn-round" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.button.login_with_fb') }}">
      <i class="fa fa-facebook-f"></i>
    </a>

    <a href="{{ route('socialite.customer', 'google') }}" class="demo-restrict btn btn-google btn-round" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.button.login_with_g') }}">
      <i class="fa fa-google"></i>
    </a>

    @if (is_incevio_package_loaded('apple-login'))
      <a href="{{ route('socialite.customer', 'apple') }}" class="demo-restrict btn btn-apple btn-round" data-toggle="tooltip" data-placement="top" title="{{ trans('packages.apple-login.login_with_apple') }}">
        <i class="fa fa-apple"></i>
      </a>
    @endif
  </div>
@endif
