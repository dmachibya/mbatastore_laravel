@if (isset($banners['bottom']))
  <section>
    <div class="container w-100">
      <div class="row mt-3">
        @foreach ($banners['bottom'] as $banner)
          @include('theme::layouts.banner', $banner)
        @endforeach
      </div><!-- /.row -->
    </div><!-- /.container -->
  </section>
@endif
