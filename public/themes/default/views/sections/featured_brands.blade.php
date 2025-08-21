@if (count($featured_brands))
  <section>
    <div class="feature-brand">
      <div class="container">
        <div class="feature-brand-inner">
          <div class="sell-header">
            <div class="sell-header-title">
              <h2>
                {{ trans('theme.featured_brand') }}
                <i class="fal fa-crown menu-icon"></i>
              </h2>
            </div>
            <div class="header-line">
              <span></span>
            </div>
          </div> <!-- /.sell-header -->

          <div class="feature-brand-content">
            <div class="row justify-content-center">
              @foreach ($featured_brands as $brand)
                <div class="col-lg-2 col-sm-3 col-4 mt-4 fbrnd-card">
                  {{-- <div class="feature-brand-img"> --}}
                  <a href="{{ route('show.brand', $brand->slug) }}">
                    <img class="lazy img-thumbnail" src="{{ get_storage_file_url(optional($brand->featureImage)->path, 'tyni') }}" data-src="{{ get_storage_file_url(optional($brand->featureImage)->path, 'full') }}" alt="{{ $brand->name }}">
                  </a>
                  {{-- </div> --}}
                </div>
              @endforeach
            </div>
          </div>
        </div> <!-- /.feature-brand-inner -->
      </div> <!-- /.container -->
    </div> <!-- /.feature-brand -->
  </section>
@endif
