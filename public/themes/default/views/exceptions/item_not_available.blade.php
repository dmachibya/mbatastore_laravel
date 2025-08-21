@extends('theme::layouts.main')

@section('content')
  <section>
    <div class="container">
      <p class="lead text-center my-5">
        {!! trans('theme.item_not_available') !!}<br /><br />
        <a href="{{ url('/') }}" class="btn btn-primary btn-sm">@lang('theme.button.shop_from_other_categories')</a>
      </p>
    </div> <!-- /.container -->
  </section>

  <!-- BROWSING ITEMS -->
  @include('theme::sections.recent_views')
@endsection
