@isset($recent)
  <section>
    <div class="neckbands">
      <div class="container md-100">
        <div class="neckbands-inner">
          <div class="neckbands-header">
            <div class="sell-header mb-3">
              <div class="sell-header-title">
                <h2>
                  {{ trans('theme.recently_added') }}
                  <i class="far fa-clock"></i>
                </h2>
              </div>

              <div class="header-line">
                <span></span>
              </div>

              <div class="best-deal-arrow">
                <ul>
                  <li><button class="left-arrow slider-arrow slick-arrow neckbands-left" aria-label="left arrow"><i class="fal fa-chevron-left"></i></button></li>
                  <li><button class="right-arrow slider-arrow slick-arrow neckbands-right" aria-label="right arrow"><i class="fal fa-chevron-right"></i></button></li>
                </ul>
              </div>
            </div>
          </div> <!-- /.neckbands-header -->

          <div class="neckband-items">
            <div class="recent-items-inner">
              @include('theme::partials._product_horizontal', ['products' => $recent, 'ratings' => 1])
            </div>
          </div> <!-- /.neckbands-items -->
        </div> <!-- /.neckbands-inner -->
      </div> <!-- /.container -->
    </div> <!-- /.neckbands -->
  </section>
@endisset
