<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @include('meta')

  <link rel="canonical" href="{{ url()->current() }}">

  <!-- Main custom css -->
  <link href="{{ theme_asset_url('css/vendors.css') }}" media="screen" rel="stylesheet">
  <link href="{{ theme_asset_url('css/style.css') }}" media="screen" rel="stylesheet">

  @if (config('active_locales') && config('active_locales')->firstWhere('code', App::getLocale())->rtl)
    <link href="{{ theme_asset_url('css/rtl.css') }}" media="screen" rel="stylesheet">
  @endif

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  {{-- <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]--> --}}

  {{-- Custom CSS for shop profile page only --}}
  @if (Request::is('shop/*') && isset($shop) && get_custom_css($shop->id))
    <style>
      {{ get_custom_css($shop->id) }}
    </style>
  @endif

  {{-- Custom CSS --}}
  @if (get_custom_css())
    <style>
      {{ get_custom_css() }}
    </style>
  @endif

  @if (is_incevio_package_loaded('otp-login'))
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
  @endif

  <style>
    .iti {
      display: block;
    }
  </style>

  <link rel="preconnect" href="https://maxcdn.bootstrapcdn.com/">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>

<body class="{{ config('active_locales')->firstWhere('code', App::getLocale())->rtl ? 'rtl' : 'ltr' }}">

  <!-- Google Tag Manager (noscript) -->
  @if (config('services.google.gtm_container_id'))
    <noscript>
      <iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.google.gtm_container_id') }}" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
  @endif
  <!-- End Google Tag Manager (noscript) -->

  <!--[if lte IE 9]>
        <p class="p-4">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->

  <!-- Wrapper start -->
  <div class="wrapper">
    {{-- <div class="overlay"></div>  --}}
    <!-- Overlay to maintain focus on nagitation -->

    <!-- VALIDATION ERRORS -->
    @if (count($errors) > 0)
      <div class="alert alert-danger alert-dismissible mb-0" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

        <strong>{{ trans('theme.error') }}!</strong> {{ trans('messages.input_error') }}<br><br>

        <ul class="list-group">
          @foreach ($errors->all() as $error)
            <li class="list-group-item list-group-item-danger">{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Top promo bar -->
    @include('front_top_promo')

    <!-- Announcement -->
    @if (is_incevio_package_loaded('announcement'))
      @include('announcement::announcement')
    @endif

    <!-- Header start -->
    <header class="header">
      <!-- Primary Menu -->
      @include('theme::nav.main')

      <!-- Mobile Menu -->
      @include('theme::nav.mobile')
      <div class="close-sidebar">
        <strong><i class="fal fa-times"></i></strong>
      </div>
    </header>

    <div id="content-wrapper">
      @yield('content')
    </div>

    <div id="loading">
      <img id="loading-image" src="{{ theme_asset_url('img/loading.gif') }}" alt="busy...">
    </div>

    <!-- Quick View Modal-->
    <div id="quickViewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false"></div>

    <!-- my Dynamic Modal-->
    <div id="myDynamicModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false"></div>

    <!-- footer start -->
    @include('theme::nav.footer')
  </div>
  <!-- Wrapper end -->

  <!-- MODALS -->
  @unless (Auth::guard('customer')->check())
    @include('theme::auth.modals')
  @endunless

  <script src="{{ theme_asset_url('js/app.js') }}"></script>

  {{--  Toast notification --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

  @include('scripts.demo_restrict')

  @include('theme::notifications')

  <!-- AppJS -->
  @include('theme::scripts.appjs')

  {{-- Announcement script --}}
  @if (is_incevio_package_loaded('announcement'))
    @include('announcement::script')
  @endif

  <!-- otp-login scripts -->
  @if (is_incevio_package_loaded('otp-login'))
    @include('otp-login::scripts')
  @endif

  {{-- Search Autocomplete script --}}
  @if (is_incevio_package_loaded('searchAutocomplete'))
    @include('searchAutocomplete::scripts')
  @endif

  {{-- Comparison script --}}
  @if (is_incevio_package_loaded('comparison'))
    @include('comparison::script')
  @endif

  <!-- Page Scripts -->
  @yield('scripts')

  {{-- Purchase button popup --}}
  @if (config('app.demo') == true && \Str::contains(url()->current(), 'zcart'))
    @include('partials.demo_purchase_btn')
  @endif
  @include('partials._theme_change_btns')

  <script>
    // Dynamic theme colors
    const setTheme = theme => document.documentElement.setAttribute('theme', theme);
    document.getElementById('zcart-js-theme-select').addEventListener('change', function() {
      setTheme(this.value);
    });

    document.addEventListener("DOMContentLoaded", function() {
      var lazyImages = [].slice.call(document.querySelectorAll(".lazy"));

      if ("IntersectionObserver" in window) {
        let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
          entries.forEach(function(entry) {
            if (entry.isIntersecting) {
              let lazyImage = entry.target;
              lazyImage.src = lazyImage.dataset.src;
              lazyImage.classList.remove("lazy");
              lazyImageObserver.unobserve(lazyImage);
            }
          });
        });

        lazyImages.forEach(function(lazyImage) {
          lazyImageObserver.observe(lazyImage);
        });
      } else {
        // Possibly fall back to a more compatible method here
      }
    });
  </script>
</body>

</html>
