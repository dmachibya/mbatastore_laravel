<div class="row">
  <div class="cover-img-wrapper">
    <img class="lazy w-100" src="{{ get_cover_img_src($shop, 'shop', 'cover_thumb') }}" data-src="{{ get_cover_img_src($shop, 'shop') }}">
  </div>
</div> <!-- /.row -->

<div class="container lg-100">
  <div id="profile-container" class="row">
    <div class="col-12 mb-5 px-md-1 px-0">
      <div class="row profile-header border mt-3">
        <div class="col-lg-2 col-md-3 text-center my-3">
          <div class="d-flex thumbnail rounded-circle justify-content-center align-items-center mx-auto p-2">
            <img class="lazy w-100" src="{{ get_storage_file_url(optional($shop->logoImage)->path, 'tiny') }}" data-src="{{ get_storage_file_url(optional($shop->logoImage)->path, 'full') }}" alt="{{ $shop->name }}">
          </div>
        </div> <!-- /.col -->

        <div class="col-lg-6 col-md-9 profile-info">
          <div class="header-fullname">
            {!! $shop->getQualifiedName() !!}
            {!! $shop->reward_badge !!}

            <a href="javascript:void(0);" class="btn btn-primary btn-sm contact-seller-btn" data-toggle="modal" data-target="{{ Auth::guard('customer')->check() ? '#contactSellerModal' : '#loginModal' }}">
              <i class="far fa-envelope"></i> @lang('theme.button.contact_seller')
            </a>
          </div>

          @if ($shop->feedbacks->count())
            <span class="small">
              @include('theme::layouts.ratings', ['ratings' => $shop->feedbacks->avg('rating'), 'count' => $shop->feedbacks->count(), 'shop' => true])
            </span>
          @endif

          <div class="header-information show-hide-content mb-0 less">
            {!! $shop->description !!}
          </div>

          <a href="javascript::void(0)" class="small show-hide-content-btn">
            {{ trans('theme.show_more') }} <i class="fa fa-angle-down"></i>
          </a>
        </div> <!-- /.col -->

        <div class="col-lg-4 profile-stats">
          <div class="row">
            <div class="col-6 col-xs-12 stats-col">
              <div class="stats-value">{{ $shop->inventories_count }}</div>
              <div class="stats-title">{{ trans('theme.active_listings') }}</div>
            </div>

            <div class="col-6 col-xs-12 stats-col">
              <div class="stats-value">{{ $shop->total_item_sold }}</div>
              <div class="stats-title">{{ trans('theme.items_sold') }}</div>
            </div>
          </div> <!-- /.row -->

          <div class="row">
            <div class="col-6 col-xs-12 inlinestats-col">
              <i class="fa fa-map-marker mr-2"></i> {!! $shop->address->toShortString() !!}
            </div>

            <div class="col-6 col-xs-12 inlinestats-col">
              <strong>{{ trans('theme.member_since') . ' ' . $shop->created_at->toFormattedDateString() }}</strong>
            </div>
          </div> <!-- /.row -->
        </div> <!-- /.col -->
      </div> <!-- /.row .profile-header -->

      <div class="row profile-body mt-0">
        <div class="col-12 tabbable p-0">
          <ul class="nav nav-tabs m-0 nav-justified border" id="myTab11">
            <li class="border-r {{ \Request::route()->getName() == 'show.store' ? 'active' : '' }}">
              <a href="{{ route('show.store', $shop->slug) }}" aria-expanded="true">
                {{ trans('theme.shop_home') }}
              </a>
            </li>

            <li class="border-r {{ isset($products) ? 'active' : '' }}">
              <a href="{{ route('shop.products', $shop->slug) }}" aria-expanded="false">
                {{ trans('theme.products') }}
              </a>
            </li>

            @if ($shop->config->return_refund)
              <li class="border-r">
                <a data-toggle="tab" href="#return-policy-tab" aria-expanded="false">
                  {{ trans('theme.return_and_refund_policy') }}
                </a>
              </li>
            @endif

            <li class="{{ isset($reviews) ? 'active' : '' }}">
              <a href="{{ route('shop.reviews', $shop->slug) }}" aria-expanded="false">
                {{ trans('theme.latest_reviews') }}
              </a>
            </li>
          </ul> <!-- /.profile-header -->

          <div class="tab-content mt-3">
            <div id="overview-tab" class="tab-pane {{ \Request::route()->getName() == 'show.store' ? 'active' : '' }}">
              <!-- SLIDER -->
              @include('theme::sections.slider_shop_page')

              <!-- banner grp one -->
              @if (!empty($banners['group_1']))
                @include('theme::sections.banners', ['banners' => $banners['group_1']])
              @endif

              <!-- Top selling -->
              @isset($top_items)
                <section>
                  <div class="neckbands">
                    <div class="container">
                      <div class="neckbands-inner">
                        <div class="neckbands-header">
                          <div class="sell-header">
                            <div class="sell-header-title">
                              <h2>{{ trans('theme.top_selling') }}</h2>
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
                            @include('theme::partials._product_horizontal', ['products' => $top_items, 'ratings' => 1])
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </section>
              @endisset

              <!-- Deal of Day start -->
              @include('theme::sections.deal_of_the_day')

              <!-- Recently Added -->
              @include('theme::sections.recently_added')

              <!-- banner grp three -->
              @if (!empty($banners['group_2']))
                <div class="mt-3">
                  @include('theme::sections.banners', ['banners' => $banners['group_2']])
                </div>
              @endif

              <!-- Best finds under $99 deals start -->
              @if (isset($deals_under) && count($deals_under))
                <section>
                  <div class="best-deals">
                    <div class="container">
                      <div class="best-deals-inner">
                        <div class="best-deals-header">
                          <div class="sell-header">
                            <div class="sell-header-title">
                              <h2>
                                {{ trans('theme.best_find_under', ['amount' => get_formated_currency(get_from_option_table('best_finds_under' . $shop->id))]) }}
                              </h2>
                            </div>
                            <div class="header-line">
                              <span></span>
                            </div>
                            <div class="best-deal-arrow">
                              <ul>
                                <li><button class="left-arrow slider-arrow slick-arrow best-deal-left"><i class="fal fa-chevron-left"></i></button></li>
                                <li><button class="right-arrow slider-arrow slick-arrow best-deal-right"><i class="fal fa-chevron-right"></i></button></li>
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
            </div> <!-- /#overview-tab -->

            <div id="products-tab" class="tab-pane {{ isset($products) ? 'active' : '' }}">

              @include('theme::contents.product_list', ['colum' => 3])

            </div> <!-- /#products-tab -->

            <div id="return-policy-tab" class="tab-pane">
              <div class="row html-content pt-5">
                <div class="col-md-8 col-md-offset-2">
                  {!! $shop->config->return_refund !!}
                </div>
              </div> <!-- /.row -->
            </div> <!-- /#return-policy-tab -->

            <div id="reviews-tab" class="tab-pane {{ isset($reviews) ? 'active' : '' }}">
              <div class="row">
                <div class="col-md-6 offset-md-3">
                  @isset($reviews)
                    @forelse($reviews as $review)
                      <p>
                        <b>{{ $review->customer->nice_name ?? $review->customer->name }}</b>

                        <span class="pull-right small">
                          <b class="text-success">@lang('theme.verified_purchase')</b>
                          <span class="text-muted"> | {{ $review->created_at->diffForHumans() }}</span>
                        </span>
                      </p>

                      <p>{{ $review->comment }}</p>

                      @include('theme::layouts.ratings', ['ratings' => $review->rating])

                      @unless ($loop->last)
                        <hr class="dotted" />
                      @endunless
                    @empty
                      <p class="lead text-center text-muted">@lang('theme.no_reviews')</p>
                    @endforelse

                    <div class="row d-flex justify-content-center pagenav-wrapper mt-5 mb-3">
                      {{ $reviews->links('theme::layouts.pagination') }}
                    </div><!-- /.row .pagenav-wrapper -->
                  @endisset
                </div>
              </div> <!-- /.row -->
            </div> <!-- /#reviews-tab -->
          </div> <!-- /.tab-content -->
        </div> <!-- /.col-lg-12 -->
      </div> <!-- /.row .profile-body -->
    </div>
  </div>
</div>
