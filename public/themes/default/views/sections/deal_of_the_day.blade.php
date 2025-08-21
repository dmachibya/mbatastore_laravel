@if (isset($deal_of_the_day) && $deal_of_the_day)
  <section>
    <div class="best-deal">
      <div class="container">
        <div class="best-deal-inner">
          <div class="row">
            <div class="col-xl-{{ $featured_items ? '8' : '12' }}">
              <div class="box-inner">
                <div class="best-deal-header">
                  <div class="sell-header">
                    <div class="sell-header-title">
                      <h2 class="mb-1">
                        {{ trans('theme.deal_of_the_day') }}
                        <i class="fal fa-calendar-day"></i>
                      </h2>
                    </div>

                    <div class="header-line">
                      <span></span>
                    </div>
                  </div>
                </div>

                <div class="deal-of-day">
                  <div class="deal-of-day-label">{{ trans('theme.hot') }}</div>
                  <div class="deal-of-day-inner">
                    <div class="deal-of-day-slider deal-slider text-center">
                      @foreach ($deal_of_the_day->images as $img)
                        <div class="deal-of-day-slider-item">
                          <img class="lazy" src="/images/square.webp" data-src="{{ get_storage_file_url($img->path, 'full') }}" alt="{{ $deal_of_the_day->title }}">
                        </div>
                      @endforeach
                    </div>

                    <div class="deal-of-day-details">
                      <div class="deal-of-day-details-name">
                        <a href="{{ route('show.product', $deal_of_the_day->slug) }}">{!! strip_tags($deal_of_the_day->title) !!}</a>
                      </div>

                      <div class="deal-of-day-details-price">
                        <p>
                          <span class="regular-price">
                            {!! get_formated_currency($deal_of_the_day->current_sale_price(), config('system_settings.decimals', 2)) !!}
                          </span>

                          @if ($deal_of_the_day->hasOffer())
                            <span class="old-price">
                              {!! get_formated_currency($deal_of_the_day->sale_price, config('system_settings.decimals', 2)) !!}
                            </span>
                          @endif
                        </p>
                      </div>

                      <div class="best-seller-item-rating">
                        @include('theme::partials._vertical_ratings', ['ratings' => $deal_of_the_day->ratings])
                      </div>

                      <div class="deal-of-day-details-description">
                        <p>{{ substr(strip_tags($deal_of_the_day->description), 0, 100) }}</p>
                      </div>

                      <div class="deal-of-day-details-list">
                        <h3>{{ trans('theme.key_features') }}</h3>
                        <ul>
                          @foreach (unserialize($deal_of_the_day->key_features) as $t_key_feature)
                            <li>
                              <i class="fal fa-check-double"></i>
                              <span>{{ $t_key_feature }}</span>
                            </li>
                          @endforeach
                        </ul>
                      </div>

                      <div class="deal-of-day-btns mt-4">
                        <a href="javascript:void(0);" data-link="{{ route('cart.addItem', $deal_of_the_day->slug) }}" class="sc-add-to-cart" tabindex="0">
                          <i class="fal fa-shopping-cart"></i>
                          <span class="d-none d-sm-inline-block">{{ trans('theme.add_to_cart') }}</span>
                        </a>

                        <a href="javascript:void(0);" data-link="{{ route('wishlist.add', $deal_of_the_day) }}" class="add-to-wishlist">
                          <i class="far fa-heart"></i> {{ trans('theme.button.add_to_wishlist') }}
                        </a>

                        @if (is_incevio_package_loaded('comparison'))
                          {{-- @include('comparison::_btn_add_to_compare', ['item' => $deal_of_the_day]) --}}

                          <a href="javascript:void(0);" data-link="{{ route('comparable.add', $deal_of_the_day->id) }}" class="add-to-product-compare ml-3">
                            <i class="fal fa-balance-scale"></i> @lang('theme.button.compare')
                          </a>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> <!-- .col-lg-* -->

            @if ($featured_items)
              <div class="col-xl-4 mt-3 mt-xl-0">
                <div class="best-deal-col">
                  <div class="best-deal-header">
                    <div class="sell-header">
                      <div class="sell-header-title">
                        <h2>
                          {{ trans('theme.featured_items') }}
                          <i class="far fa-hat-cowboy"></i>
                        </h2>
                      </div>
                      <div class="header-line">
                        <span></span>
                      </div>

                      <div class="best-deal-arrow">
                        <ul>
                          <li>
                            <button class="left-arrow slider-arrow best-seller-left" aria-label="left arrow">
                              <i class="fal fa-chevron-left"></i>
                            </button>
                          </li>

                          <li>
                            <button class="right-arrow slider-arrow best-seller-right" aria-label="right arrow">
                              <i class="fal fa-chevron-right"></i>
                            </button>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div> <!-- /.best-deal-header -->

                  <div class="best-seller">
                    <div class="best-seller-slider">
                      <div class="sl">
                        @foreach ($featured_items as $item)
                          <div class="best-seller-item border-animate" data-mycount="{{ $loop->iteration }}">
                            <div class="box-inner">
                              <div class="best-seller-item-image">
                                <a href="{{ route('show.product', $item->slug) }}">
                                  <img class="lazy" src="/images/square.webp" data-src="{{ get_inventory_img_src($item, 'medium') }}" data-name="product_image" alt="{{ $item->title }}" title="{{ $item->title }}">
                                </a>
                              </div>

                              <div class="best-seller-item-details">
                                <div class="best-seller-item-details-inner">
                                  <div class="best-seller-item-name">
                                    <a href="{{ route('show.product', $item->slug) }}">
                                      {{ $item->title }}
                                    </a>
                                  </div>

                                  <div class="best-seller-item-rating">
                                    @include('theme::partials._vertical_ratings', ['ratings' => $item->ratings])
                                  </div>

                                  <div class="best-seller-item-price">
                                    @include('theme::partials._home_pricing')
                                  </div>

                                  <div class="best-seller-item-utility">
                                    <div class="box-action-price mb-1">
                                      @include('theme::partials._home_pricing')
                                    </div>

                                    <div class="horizon-btns">
                                      @include('theme::partials._horizontal_action_buttons')
                                    </div>
                                  </div> <!-- /.best-seller-item-utility -->
                                </div> <!-- /.best-seller-item-details-inner -->
                              </div> <!-- /.best-seller-item-details -->
                            </div> <!-- /.box-inner -->
                          </div> <!-- /.best-seller-item -->

                          @if ($loop->iteration % 4 === 0)
                      </div> <!-- /.sl -->
                      <div class="sl">
            @endif
@endforeach
</div> <!-- /.sl -->
</div> <!-- /.best-seller-slider -->
</div> <!-- /.best-seller -->
</div> <!-- /.best-deal-col -->
</div> <!-- /.col-lg-4 -->
@endif
</div> <!-- /.row -->
</div> <!-- /.best-deal-inner -->
</div> <!-- /.container -->
</div> <!-- /.best-deal -->
</section>
@elseif(isset($featured_items) && $featured_items)
<section>
  <div class="neckbands">
    <div class="container">
      <div class="neckbands-inner">
        <div class="neckbands-header">
          <div class="sell-header">
            <div class="sell-header-title">
              <h2>{{ trans('theme.featured') }}</h2>
            </div>
            <div class="header-line">
              <span></span>
            </div>
            <div class="best-deal-arrow">
              <ul>
                <li><button class="left-arrow slider-arrow slick-arrow neckbands-left"><i class="fal fa-chevron-left"></i></button></li>
                <li><button class="right-arrow slider-arrow slick-arrow neckbands-right"><i class="fal fa-chevron-right"></i></button></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="neckbands-items">
          <div class="neckbands-items-inner">
            @include('theme::partials._product_horizontal', ['products' => $featured_items])
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endif
