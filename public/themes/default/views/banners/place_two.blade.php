@if (isset($banners['place_two']))
  <div class="row featured mt-3">
    @foreach ($banners['place_two'] as $banner)
      @include('theme::layouts.banner', $banner)
    @endforeach
  </div><!-- /.row -->
@endif
