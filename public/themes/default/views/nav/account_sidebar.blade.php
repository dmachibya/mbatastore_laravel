<div class="account-sidebar-nav-title btn border mr-3 text-left mb-2">
  <em class="fa fa-bars"></em>
</div>

<ul class="account-sidebar-nav radius bg-light">
  <li class="{{ $tab == 'dashboard' ? 'active' : '' }}">
    <a href="{{ route('account', 'dashboard') }}">
      <i class="fas fa-tachometer-alt no-fill"></i> @lang('theme.nav.dashboard')
    </a>
  </li>

  @if (customer_has_wallet())
    <li class="{{ $tab == 'wallet' || $tab == 'deposit' ? 'active' : '' }}">
      <a href="{{ route('customer.account.wallet') }}">
        <i class="fas fa-wallet no-fill"></i> @lang('packages.wallet.my_wallet')
        @include('partials._addon_badge')
      </a>
    </li>
  @endif

  <li class="{{ $tab == 'messages' || $tab == 'message' ? 'active' : '' }}">
    <a href="{{ route('account', 'messages') }}"><i class="fas fa-envelope no-fill"></i> @lang('theme.my_messages')</a>
  </li>

  <li class="{{ $tab == 'orders' ? 'active' : '' }}">
    <a href="{{ route('account', 'orders') }}"><i class="fas fa-shopping-cart no-fill"></i> @lang('theme.nav.my_orders')</a>
  </li>

  {{-- <li class="{{ $tab == 'downloadables' ? 'active' : '' }}">
    <a href="{{ route('account', 'downloadables') }}"><i class="fas fa-download no-fill"></i> @lang('theme.downloadables')</a>
  </li> --}}

  <li class="{{ $tab == 'wishlist' ? 'active' : '' }}">
    <a href="{{ route('account', 'wishlist') }}">
      <i class="fas fa-heart no-fill"></i> @lang('theme.nav.my_wishlist')
    </a>
  </li>

  <li class="{{ $tab == 'disputes' ? 'active' : '' }}">
    <a href="{{ route('account', 'disputes') }}"><i class="fas fa-rocket no-fill"></i> @lang('theme.nav.refunds_disputes')</a>
  </li>

  <li class="{{ $tab == 'coupons' ? 'active' : '' }}">
    <a href="{{ route('account', 'coupons') }}">
      <i class="fas fa-tags no-fill"></i> @lang('theme.nav.my_coupons')
    </a>
  </li>

  @if (is_incevio_package_loaded('eventy'))
    <li class="{{ $tab == 'events' ? 'active' : '' }}">
      <a href="{{ route('account', 'events') }}">
        <i class="fas fa-calendar no-fill"></i> @lang('packages.eventy.my_events')
        @include('partials._addon_badge')
      </a>
    </li>
  @endif

  {{-- <li class="{{ $tab == 'gift_cards' ? 'active' : '' }}">
        <a href="{{ route('account', 'gift_cards') }}"><i class="fas fa-gift no-fill"></i> @lang('theme.nav.gift_cards')</a>
    </li> --}}

  <li class="{{ $tab == 'account' ? 'active' : '' }}">
    <a href="{{ route('account', 'account') }}">
      <i class="fas fa-user no-fill"></i> @lang('theme.nav.my_account')
    </a>
  </li>
</ul>
