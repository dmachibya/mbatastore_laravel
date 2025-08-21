@if (isset($digital_products) && $digital_products->count() > 0)
  <section>
    <div class="neckbands">
      <div class="container">
        <div class="neckbands-inner">
          <div class="neckbands-header">
            <div class="sell-header">
              <div class="sell-header-title">
                <h2>
                  {{ trans('theme.digital_products') }}
                  <i class="far fa-cloud-download-alt"></i>
                  {{-- <i class="far fa-file-archive"></i> --}}
                </h2>
              </div>

              <div class="header-line">
                <span></span>
              </div>

              <div class="best-deal-arrow">
                <ul>
                  <li><button class="left-arrow slider-arrow slick-arrow digital-left" aria-label="left arrow"><i class="fal fa-chevron-left"></i></button></li>
                  <li><button class="right-arrow slider-arrow slick-arrow digital-right" aria-label="right arrow"><i class="fal fa-chevron-right"></i></button></li>
                </ul>
              </div>
            </div>
          </div> <!-- /.neckbands-header -->

          <div class="digital-products-items">
            <div class="digital-products-items-inner">
              @include('theme::partials._product_horizontal', ['products' => $digital_products, 'ratings' => 1])
            </div>
          </div> <!-- /.neckbands-items -->
        </div> <!-- /.neckbands-inner -->
      </div> <!-- /.container -->
    </div> <!-- /.neckbands -->
  </section>
@endif
