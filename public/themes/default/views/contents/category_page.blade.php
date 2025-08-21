<section>
  <div class="container category-single-page">
    @include('theme::contents.product_list', ['colum' => 3])

    @if (config('system_settings.show_seo_info_to_frontend'))
      <div class="row">
        <div class="col-md-3">
        </div><!-- /.col-sm-3 -->

        <div class="col-md-9 py-3">
          <span class="lead">{{ $category->meta_title }}</span>
          <p>{{ $category->meta_description }}</p>
        </div><!-- /.col-sm-9 -->
      </div><!-- /.row -->
    @endif
  </div><!-- /.container -->
</section>
