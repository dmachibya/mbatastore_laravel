@if (isset($banners['place_three']))
  <div class="row featured mt-3">
    @foreach ($banners['place_three'] as $banner)
      @include('theme::layouts.banner', $banner)
    @endforeach
  </div><!-- /.row -->
@endif
