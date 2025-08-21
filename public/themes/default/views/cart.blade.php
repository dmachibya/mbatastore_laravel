@extends('theme::layouts.main')

@section('content')
  <!-- breadcrumb -->
  @include('theme::headers.cart_page')

  <!-- CONTENT SECTION -->
  @include('theme::contents.cart_page')

  <!-- BROWSING ITEMS -->
  @include('theme::sections.recent_views')
@endsection

@section('scripts')
  @include('theme::modals.ship_to')
  @include('theme::scripts.cart')

  @if (is_incevio_package_loaded('wholesale'))
    @include('wholesale::scripts.cart_page_script')
  @endif

  @include('theme::scripts.dynamic_checkout')
@endsection
