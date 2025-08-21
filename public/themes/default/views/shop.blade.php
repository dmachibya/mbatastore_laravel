@extends('theme::layouts.main')

@section('content')
  <!-- CONTENT SECTION -->
  @include('theme::contents.shop_page')

  <!-- MODALS -->
  {{-- @include('theme::modals.shopReviews') --}}

  @if (Auth::guard('customer')->check())
    @include('theme::modals.contact_seller', ['shop' => $shop])
  @endif
@endsection

@section('scripts')
  @if (is_incevio_package_loaded('liveChat') && is_chat_enabled($shop))
    @if (isset($shop->fb_page_id))
      @include('liveChat::facebook.script', ['fb_page_id' => $shop->fb_page_id]);
    @else
      @include('liveChat::livechat', ['shop' => $shop, 'agent' => $shop->owner, 'agent_status' => trans('theme.online')])
    @endif
  @endif
@endsection
