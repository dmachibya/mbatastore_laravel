@if (Auth::guard('web')->check())
  @desktop
    <style>
      #zcart_admintopnav {
        position: relative;
        background: #1d2327;
        background-color: #1d2327;
        color: #c3c4c7;
        font-weight: 400;
        font-size: 13px;
        width: 100%;
        overflow: hidden;
        z-index: 4;
      }

      /* Style the links inside the navigation bar */
      #zcart_admintopnav a {
        float: left;
        color: #f2f2f2;
        text-align: center;
        text-decoration: none;
        padding: 4px 16px;
      }

      /* Change the color of links on hover */
      #zcart_admintopnav a:hover {
        background-color: #04AA6D;
        color: #fff;
      }

      /* Create a right-aligned (split) link inside the navigation bar */
      #zcart_admintopnav a.split {
        float: right;
      }

      #zcart_admintopnav a.split.highlight {
        background-color: #04AA6D;
        color: #fff;
      }
    </style>

    <div id="zcart_admintopnav">
      <a class="active" href="{{ url('admin/dashboard') }}">
        <i class="fa fa-fw fa-dashboard"></i> {{ trans('nav.dashboard') }}
      </a>

      <a href="{{ url('admin/order/order') }}">
        <i class="fa fa-fw fa-shopping-cart"></i> {{ trans('nav.orders') }}
      </a>

      @if (Auth::user()->isAdmin())
        <a href="{{ url('admin/appearance/theme') }}">
          <i class="fa fa-fw fa-paint-brush"></i> {{ trans('nav.appearance') }}
        </a>

        <a href="{{ route('admin.kpi') }}">
          <i class="fa fa-fw fa-bar-chart"></i> {{ trans('nav.reports') }}
        </a>
      @elseif(Auth::user()->isMerchant())
        <a href="{{ url('admin/stock/inventory') }}">
          <i class="fa fa-fw fa-cubes"></i> {{ trans('nav.inventories') }}
        </a>

        <a href="{{ route('admin.shop-kpi') }}">
          <i class="fa fa-fw fa-bar-chart"></i> {{ trans('nav.reports') }}
        </a>
      @endif

      <a href="{{ route('logout') }}" class="split">
        <i class="fa fa-fw fa-sign-out"></i> {{ trans('nav.logout') }}
      </a>

      <a href="{{ route('admin.account.profile') }}" class="split highlight">
        {{ trans('app.welcome') . ' ' . Auth::user()->getName() }}
      </a>
    </div>
  @enddesktop
@endif
@if (is_incevio_package_loaded('affiliate') && auth()->guard('affiliate')->check())
    @include('affiliate::frontend.top_nav')
@endif