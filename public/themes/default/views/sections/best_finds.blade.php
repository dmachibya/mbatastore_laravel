@if (isset($deals_under) && count($deals_under))
  <section>
    <div class="best-deals">
      <div class="container">
        <div class="best-deals-inner">
          <div class="best-deals-header">
            <div class="sell-header">
              <div class="sell-header-title">
                <h2>
                  {{ trans('theme.best_find_under', ['amount' => get_formated_currency(get_from_option_table('best_finds_under'))]) }}
                  <i class="far fa-search-dollar"></i>
                  {{-- <i class="fas fa-piggy-bank"></i> --}}
                </h2>
              </div>
              <div class="header-line">
                <span></span>
              </div>
              <div class="best-deal-arrow">
                <ul>
                  <li><button class="left-arrow slider-arrow slick-arrow best-deal-left" aria-label="left arrow"><i class="fal fa-chevron-left"></i></button></li>
                  <li><button class="right-arrow slider-arrow slick-arrow best-deal-right" aria-label="right arrow"><i class="fal fa-chevron-right"></i></button></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="best-deals-items">
            <div class="best-deals-items-inner">

              @include('theme::partials._product_horizontal', ['products' => $deals_under, 'title' => 1, 'ratings' => 1, 'hover' => 1])

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endif
