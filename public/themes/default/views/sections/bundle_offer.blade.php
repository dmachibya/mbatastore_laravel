<section>
  <div class="bundle">
    <div class="container">
      <div class="bundle-inner">
        <div class="bundle-header">
          <div class="sell-header">
            <div class="sell-header-title">
              <h2>{{ trans('theme.bundle_offer') }}</h2>
            </div>
            <div class="header-line">
              <span></span>
            </div>
            <div class="best-deal-arrow">
              <ul>
                <li><button class="left-arrow slider-arrow slick-arrow bundle-left"><i class="fal fa-chevron-left"></i></button></li>
                <li><button class="right-arrow slider-arrow slick-arrow bundle-right"><i class="fal fa-chevron-right"></i></button></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="bundle-items">
          <div class="bundle-items-inner">
            @include('theme::partials._product_horizontal', ['products' => $bundle_offer, 'hover' => 1])
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
