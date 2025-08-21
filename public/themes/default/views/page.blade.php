@extends('theme::layouts.main')

@section('content')
  <!-- PAGE COVER IMAGE -->
  @include('theme::banners.page_cover')

  <!-- CONTENT SECTION -->
  <section class="mt-3">
    <div class="container">
      <div class="row">
        <div class="col-12">
          {!! $page->content !!}
        </div><!-- /.col-12 -->
      </div><!-- /.row -->
    </div><!-- /.container -->
  </section>

  <!-- For contact page only -->
  @if (\App\Models\Page::PAGE_CONTACT_US == $page->slug)
    @include('theme::contents.contact_us')
  @endif

  <!-- BROWSING ITEMS -->
  @include('theme::sections.recent_views')
@endsection
