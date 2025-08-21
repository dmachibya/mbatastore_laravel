<div class="copyright-area">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <ul class="links-list">
          @foreach ($pages->where('position', 'copyright_area') as $page)
            <li><a href="{{ get_page_url($page->slug) }}" target="_blank" rel="noopener">{{ $page->title }}</a></li>
          @endforeach

          <li><a href="{{ url('admin/dashboard') }}">{{ trans('theme.nav.merchant_dashboard') }}</a></li>
          @if (is_incevio_package_loaded('affiliate') && auth()->guard('affiliate')->check())
            <li><a href="{{ route('affiliate.dashboard')}}">{{ trans('packages.affiliate.affiliate_dashboard') }}</a></li>
          @elseif (is_incevio_package_loaded('affiliate'))
            <li><a href="{{ route('affiliate.login')}}">{{ trans('packages.affiliate.login') }}</a></li>
          @endif
          <li class="copyright-text">© {{ date('Y') }} <a href="{{ url('/') }}">{{ get_platform_title() }}</a></li>
        </ul>
      </div>
    </div>
  </div>
</div> <!-- /.copyright-area -->
