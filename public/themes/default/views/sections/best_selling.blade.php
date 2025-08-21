<section>
  <div class="recent">
    <div class="container">
      <div class="recent-inner">
        <div class="recent-header">
          <div class="sell-header">
            <div class="sell-header-title">
              <h2>@lang('theme.best_selling_now')</h2>
            </div>
            <div class="header-line">
              <span></span>
            </div>
            <div class="best-deal-arrow">
              <ul>
                <li><button class="left-arrow slider-arrow slick-arrow recent-left" aria-label="left arrow"><i class="fal fa-chevron-left"></i></button></li>
                <li><button class="right-arrow slider-arrow slick-arrow recent-right" aria-label="right arrow"><i class="fal fa-chevron-right"></i></button></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="recent-items">
          <div class="recent-items-inner">
            @include('theme::partials._product_horizontal', ['products' => $best_selling, 'hover' => 1])
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
