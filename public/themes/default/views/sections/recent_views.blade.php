@if (isset($recently_viewed_items) && count($recently_viewed_items))
  <section>
    <div class="best-under">
      <div class="container">
        <div class="best-under-inner">
          <div class="best-under-header">
            <div class="sell-header">
              <div class="sell-header-title">
                <h2>@lang('theme.recently_viewed')</h2>
              </div>
              <div class="header-line">
                <span></span>
              </div>
              <div class="best-deal-arrow">
                <ul>
                  <li>
                    <button class="left-arrow slider-arrow slick-arrow best-under-left" aria-label="left arrow"><i class="fal fa-chevron-left"></i></button>
                  </li>
                  <li>
                    <button class="right-arrow slider-arrow slick-arrow best-under-right" aria-label="right arrow"><i class="fal fa-chevron-right"></i></button>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="best-under-items">
            <div class="best-under-items-inner">
              {{-- <div class="best-under-items-box box">
                <div class="best-under-items-img box-img">
                  <a href="#"><img src="images/bfu1.png" alt=""></a>
                </div>
              </div> --}}
              @include('theme::partials._product_horizontal', ['products' => $recently_viewed_items, 'title' => 1, 'pricing' => 1, 'hover' => 1, 'ratings' => 1])
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endif
