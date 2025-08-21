@if ($flashdeals)
  <section id="flash-deal" class="mb-3">
    <div class="flash-deal pt-0">
      <div class="container">
        <div class="flash-deal-inner">
          @unless (empty($flashdeals['listings']))
            <div class="sell-header">
              <div class="sell-header-title">
                <h2 class="font-weight-bold">
                  {{ trans('theme.flash_deal') }}
                  <i class="fal fa-flash"></i>
                </h2>
              </div>

              <div class="flash-sell-timer">
                <span class="mr-2">
                  {{ trans('theme.offer_end_in') }} :
                </span>

                <div class="flash-sell-timer-time rounded">
                  <span class="deal-counter-days">0</span> {{ trans('theme.flash_deal_days') }} : <span class="deal-counter-hours">00</span> {{ trans('theme.hrs') }} : <span class="deal-counter-minutes">00</span> {{ trans('theme.mins') }} : <span class="deal-counter-seconds">00</span> {{ trans('theme.sec') }}
                </div>
              </div>

              <div class="header-line">
                <span></span>
              </div>

              <div class="best-deal-arrow">
                <ul>
                  <li>
                    <button class="left-arrow slider-arrow slick-arrow flashdeal-left" aria-label="left arrow"><i class="fal fa-chevron-left"></i></button>
                  </li>

                  <li>
                    <button class="right-arrow slider-arrow slick-arrow flashdeal-right" aria-label="right arrow"><i class="fal fa-chevron-right"></i></button>
                  </li>
                </ul>
              </div>
            </div>

            <div class="flashdeal mb-3">
              <div class="recent-inner">
                <div class="recent-items">
                  <div class="flashdeal-items-inner">

                    @include('theme::partials._product_horizontal', ['products' => $flashdeals['listings']])

                  </div>
                </div>
              </div> <!-- /.recent-inner -->
            </div>
          @endunless

          <!-- Feathered flash deal start -->
          <div class="flash-deal-product-main">
            <div class="row justify-content-center">
              @unless (empty($flashdeals['featured']))
                @foreach ($flashdeals['featured'] as $item)
                  <div class="col-12 col-md-6 col-sm-9 my-3">
                    <div class="flash-deal-product" style="{{ empty($flashdeals['products']) ? 'margin-top: 0' : '' }}">
                      <div class="flash-deal-product-inner">
                        <a class="flash-deal-product-name" href="{{ route('show.product', $item->slug) }}">
                          {{ $item->title }}
                        </a>

                        <div class="flash-deal-product-image">
                          <div class="flash-deal-product-badge">
                            <span>{{ $item->condition }}</span>
                          </div>

                          <a href="{{ route('show.product', $item->slug) }}">
                            <img class="lazy" src="/images/square.webp" data-src="{{ get_inventory_img_src($item, 'full') }}" data-name="product_image" alt="{{ $item->title }}" title="{{ $item->title }}">
                          </a>

                          {{-- <div class="flash-deal-product-utility">
                            @include('theme::partials._vertical_hover_buttons')
                          </div> --}}
                        </div>

                        <div class="flash-deal-product-details">
                          {{-- <a class="flash-deal-product-name" href="{{ route('show.product', $item->slug) }}">
                            <h3>{{ $item->title }}</h3>
                          </a> --}}

                          <div class="flash-deal-product-price">
                            <span class="currant-price">{!! get_formated_currency($item->current_sale_price(), config('system_settings.decimals', 2)) !!}</span>

                            @if ($item->hasOffer())
                              <span class="old-price ml-1">{!! get_formated_currency($item->sale_price, config('system_settings.decimals', 2)) !!}</span>

                              <span class="offer ml-3">
                                {{ trans('theme.percent_off', ['value' => $item->discount_percentage()]) }}

                                {{-- -{{ round(((($item->sale_price - $item->current_sale_price()) / $item->sale_price) * 100), 2) }}% --}}
                              </span>
                            @endif
                          </div> <!-- /.flash-deal-product-price -->

                          <div class="flash-deal-product-description">
                            <p>{!! $item->description !!}</p>
                          </div>

                          <div class="flash-deal-product-rating">
                            @include('theme::partials._vertical_ratings', ['ratings' => $item->ratings])
                          </div>

                          <div class="flash-deal-product-availability">
                            <span>{{ trans('theme.availability') }}:</span>
                            <p>{{ trans('theme.stock', ['stock' => $item->stock_quantity]) }}</p>
                          </div>

                          <div class="flash-deal-product-sell-time">
                            <h3>
                              <span>
                                <span class="deal-counter-days">0</span><br> {{ trans('theme.flash_deal_days') }}
                              </span>
                              <span class="spacing">:</span>
                              <span>
                                <span class="deal-counter-hours">00</span><br>
                                {{ trans('theme.hrs') }}
                              </span>
                              <span class="spacing">:</span>
                              <span>
                                <span class="deal-counter-minutes">00</span><br>
                                {{ trans('theme.mins') }}
                              </span>
                              <span class="spacing">:</span>
                              <span>
                                <span class="deal-counter-seconds">00</span><br>
                                {{ trans('theme.sec') }}
                              </span>
                            </h3>
                          </div>
                        </div> <!-- /.flash-deal-product-details -->

                        <div class="flash-deal-product-utility">

                          @include('theme::partials._horizontal_action_buttons')

                        </div>
                      </div> <!-- Product inner End-->
                    </div> <!-- Product End-->
                  </div> <!-- /.col-12 -->
                @endforeach
              @endunless
            </div> <!-- /.row -->
          </div> <!-- Feathered flash deal end -->
        </div>
      </div>
    </div>
  </section>
@endif
