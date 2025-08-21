<div class="product-info">
  @if ($item->isInDeals())
    <div class="flash-sell-timer product-deal-header ml-0 mb-2">
      <div class="flash-sell-timer-time bg-transparent w-100">
        <span class="ends_in_text">
          <span class="flashSellBg">
            {{ trans('theme.flash_sale') }}
          </span>
          {{ trans('theme.ends_in') }} :
        </span>
        <span class="deal-counter-days">0</span> {{ trans('theme.flash_deal_days') }} : <span class="deal-counter-hours">00</span> {{ trans('theme.hrs') }} : <span class="deal-counter-minutes">00</span> {{ trans('theme.mins') }} : <span class="deal-counter-seconds">00</span> {{ trans('theme.sec') }}
      </div>
    </div>
  @elseif($item->auctionable && $item->auction_end->isFuture())
    @include('auction::frontend.timer')
  @endif

  @if ($item->product->manufacturer->slug)
    <a href="{{ route('show.brand', $item->product->manufacturer->slug) }}" class="product-info-seller-name">
      <i class="fal fa-crown small"></i> {{ $item->product->manufacturer->name }}
    </a>
  @else
    <a href="{{ route('show.store', $item->shop->slug) }}" class="product-info-seller-name">
      <i class="far fa-store"></i> {!! $item->shop->getQualifiedName() !!}
    </a>
  @endif

  @if ('quickView.product' == Route::currentRouteName())
    <h5 class="product-info-title mt-0" data-name="product_name">
      <a href="{{ route('show.product', $item->slug) }}" class="">
        {{ $item->title }}
      </a>
    </h5>
  @else
    <h5 class="product-info-title" data-name="product_name">
      {{ $item->title }}
    </h5>
  @endif

  <div class="row">
    <div class="col-12 col-sm-6">
      @if ($item->ratings)
        @include('theme::layouts.ratings', ['ratings' => $item->ratings, 'count' => $item->ratings_count])
      @endif
    </div> <!-- /.col-* -->

    <div class="col-12 col-sm-6 text-right">
      @if (is_incevio_package_loaded('wallet'))
        @include('wallet::_credit_back_percentage_badge', ['rw_percentage' => $item->reward_percentage])
      @endif
    </div> <!-- /.col-* -->
  </div> <!-- /.row -->

  <div class="row">
    <div class="col-6 product-info-price pr-1">
      <span class="product-info-price-new">
        {!! get_formated_currency($item->current_sale_price(), config('system_settings.decimals', 2)) !!}
      </span>

      @if ($item->hasOffer())
        <span class="old-price">
          {!! get_formated_currency($item->sale_price, config('system_settings.decimals', 2)) !!}
        </span>
      @endif
    </div> <!-- /.col-* .product-info-price -->

    @if ($item->sold_quantity > 0)
      <div class="col-6 pl-1">
        <div class="sold-qtt-progress">
          <div class="progress">
            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:{{ $item->product->downloadable ? 90 : ($item->sold_quantity / $item->total_stock) * 100 }}%;" aria-valuenow="{{ $item->sold_quantity }}" aria-valuemin="0" aria-valuemax="{{ $item->product->downloadable ? 90 : $item->total_stock }}"></div>

            <span class="sold-qtt-label">
              {{ trans('theme.qtt_sold_of', ['sold' => $item->sold_quantity, 'qtt' => $item->total_stock]) }}
            </span>
          </div> <!-- /.progress -->
        </div> <!-- /.sold-qtt-progress -->
      </div> <!-- /.col-* -->
    @endif
  </div> <!-- /.row -->

  <div class="row">
    <div class="col-6 pr-1">
      <div class="product-info-availability mb-1">
        <div class="d-none d-sm-inline-block">@lang('theme.availability'):</div>
        <span>{{ $item->availability }}</span>
      </div>
    </div> <!-- /.col-* -->

    <div class="col-6 pl-1">
      <div class="product-info-condition mb-1">
        @if ($item->product->downloadable)
          <div class="d-none d-sm-inline-block">@lang('theme.product_type'):</div>
          <span id="item_condition">{{ $item->type }}</span>
        @elseif(config('system_settings.show_item_conditions'))
          <div class="d-none d-sm-inline-block">@lang('theme.condition'):</div>

          <span id="item_condition">{{ $item->condition }}</span>

          @if ($item->condition_note)
            <sup>
              <i class="fas fa-question" id="item_condition_note" data-toggle="tooltip" title="{{ $item->condition_note }}" data-placement="top"></i>
            </sup>
          @endif
        @endif
      </div> <!-- /.product-info-condition -->
    </div> <!-- /.col-* -->
  </div> <!-- /.row -->

  <div class="row mb-2">
    <div class="col-6 pr-1">
      <a href="javascript:void(0);" data-link="{{ route('wishlist.add', $item) }}" class="btn btn-link add-to-wishlist">
        <i class="far fa-heart"></i> @lang('theme.button.add_to_wishlist')
      </a>
    </div> <!-- /.col-* -->

    <div class="col-6 pl-1">
      @if ('quickView.product' == Route::currentRouteName())
        <a href="{{ route('show.store', $item->shop->slug) }}" class="btn btn-link">
          <i class="far fa-cubes"></i> @lang('theme.more_items_from_this_seller', ['seller' => $item->shop->name])
        </a>
      @else
        <a href="javascript:void(0);" class="btn btn-link" data-toggle="modal" data-target="{{ Auth::guard('customer')->check() ? '#contactSellerModal' : '#loginModal' }}">
          <i class="far fa-envelope"></i> @lang('theme.button.contact_seller')
        </a>
      @endif
    </div> <!-- /.col-* -->
  </div><!-- /.row -->
</div><!-- /.product-info -->

@if (is_incevio_package_loaded('wholesale') && !($item->wholesale_prices == null || count($item->wholesale_prices) == 0) && !Request::is('*/quickView'))
  @include('wholesale::product_page_price_table')
@endif

@include('theme::partials._btn_shares')
