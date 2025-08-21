@php
  $totalPrice = 0;
  $productSlugs = [];
@endphp
<div class="col-md-12 order-2">
  <div class="row justify-content-center">
    @foreach ($products as $item)
      <div class="col-sm-4 col-md-3 col-lg-2 mt-3">
        <div class="box border-animate">
          <div class="box-inner">
            <a href="{{ route('show.product', $item->slug) }}">
              <div class="recent-items-img box-img">
                <img class="lazy" src="/images/square.webp" data-src="{{ get_inventory_img_src($item, 'small') }}" data-name="product_image" alt="{{ $item->title }}" title="{{ $item->title }}">
              </div>
            </a>

            @if (empty($title))
              <div class="box-title">
                <a href="{{ route('show.product', $item->slug) }}">
                  {{ $item->title }}
                </a>
              </div>
            @endif

            {{-- @if (empty($ratings)) --}}
            <div class="box-ratting">
              @include('theme::partials._ratings', ['ratings' => $item->ratings])
            </div>
            {{-- @endif --}}

            @if (empty($pricing))
              <div class="box-price">
                @include('theme::partials._home_pricing')
              </div>
            @endif

            <div class="box-action-vertical">
              @include('theme::partials._btn_quick_view')

              @if (is_incevio_package_loaded('comparison'))
                @include('comparison::_btn_add_to_compare')
              @endif

              @include('theme::partials._btn_wishlist')
            </div>

            <div class="box-action">
              <div class="box-action-price my-2">
                @include('theme::partials._home_pricing')
              </div>

              @include('theme::partials._btn_add_to_cart')
            </div>
          </div> <!-- /.box-inner -->
        </div> <!-- /.box -->
      </div>

      @php
        $productSlugs[] = $item->slug;
        $totalPrice += $item->hasOffer() ? $item->sale_price : $item->current_sale_price();
      @endphp
    @endforeach
  </div>
</div>

<div class="col-md-12 order-1 text-center">
  <p class="flex-center mt-4 fbs-total">
    <span class="text-secondary"> @lang('theme.price_for_all') <strong class="text-primary ml-2">{!! get_formated_currency($totalPrice, config('system_settings.decimals', 2)) !!}</strong></span>
    <a data-link="{{ route('cart.addItem', ['slug' => json_encode($productSlugs)]) }}" class="btn btn-sm add-to-card-now-btn sc-add-to-cart ml-3">
      <i class="fal fa-shopping-cart"></i> @lang('theme.button.add_all_to_cart')
    </a>
  </p>
</div>
