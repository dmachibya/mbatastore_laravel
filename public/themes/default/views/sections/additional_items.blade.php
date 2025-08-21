<section>
  <div class="top-rated">
    <div class="container">
      <div class="top-rated-inner">
        <div class="top-rated-header">
          <div class="sell-header">
            <div class="sell-header-title">
              <h2>
                {{ trans('theme.system_picked_item') }}
                <i class="far fa-heart"></i>
              </h2>
            </div>
            <div class="header-line">
              <span></span>
            </div>

            <div class="best-deal-arrow">
              <ul>
                <li><button class="left-arrow slider-arrow slick-arrow top-rated-left" aria-label="left arrow"><i class="fal fa-chevron-left"></i></button></li>
                <li><button class="right-arrow slider-arrow slick-arrow top-rated-right" aria-label="right arrow"><i class="fal fa-chevron-right"></i></button></li>
              </ul>
            </div> <!-- /.best-deal-arrow -->
          </div> <!-- /.sell-header -->
        </div> <!-- /.top-rated-header -->

        <div class="top-rated-items">
          <div class="top-rated-items-inner">

            @include('theme::partials._product_horizontal', ['products' => $additional_items])

          </div>
        </div> <!-- /.top-rated-items -->
      </div> <!-- /.top-rated-inner -->
    </div> <!-- /.container -->
  </div> <!-- /.top-rated -->
</section>
