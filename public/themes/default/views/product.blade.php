@extends('theme::layouts.main')

@section('content')
  <!-- HEADER SECTION -->
  @include('theme::headers.product_page', ['product' => $item])

  <!-- CONTENT SECTION -->
  @if ($item->deleted_at == null)
    @include('theme::contents.product_page')
  @else
    <section>
      <div class="container">
        <p class="lead text-center my-5">
          {!! trans('theme.item_not_available') !!}<br /><br />
          <a href="{{ url('/') }}" class="btn btn-primary btn-sm">@lang('theme.button.shop_from_other_categories')</a>
        </p>
      </div> <!-- /.container -->
    </section>
  @endif

  <!-- RELATED ITEMS -->
  <section>
    <div class="feature">
      <div class="container">
        <div class="feature-inner">
          <div class="feature-header">
            <div class="sell-header">
              <div class="sell-header-title">
                <h2>{!! trans('theme.related_items') !!}</h2>
              </div>
              <div class="header-line">
                <span></span>
              </div>
              <div class="header-line">
                <span></span>
              </div>
              <div class="best-deal-arrow">
              </div>
            </div>
          </div>

          <div class="feature-items">
            <div class="feature-items-inner">

              @include('theme::partials._product_horizontal', ['products' => $related])

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BROWSING ITEMS -->
  @include('theme::sections.recent_views')

  <!-- MODALS -->
  @include('theme::modals.shopReviews', ['shop' => $item->shop])

  @if (Auth::guard('customer')->check())
    @include('theme::modals.contact_seller', ['shop' => $item->shop, 'item' => $item])
  @endif
@endsection

@section('scripts')
  @if (is_incevio_package_loaded('liveChat') && is_chat_enabled($item->shop))
    @if (isset($item->shop->fb_page_id))
      @include('liveChat::facebook.script', ['fb_page_id' => $item->shop->fb_page_id]);
    @else
      @include('liveChat::livechat', ['shop' => $item->shop, 'agent' => $item->shop->owner, 'agent_status' => trans('theme.online')])
    @endif
  @endif

  @include('theme::modals.ship_to')
  @include('theme::scripts.product_page')
  @include('scripts.flash_deal')

  @if (is_incevio_package_loaded('auction') && $item->auctionable)
    @include('auction::frontend.script')
  @endif
@endsection
