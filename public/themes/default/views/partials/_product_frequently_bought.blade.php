@php
  $t_totalPrice = 0;
  $t_productSlugs = [];
@endphp
<div class="row justify-content-center">
  @foreach ($products as $item)
    <div class="col mt-3">
      <a href="{{ route('show.product', $item->slug) }}">
        <div class="recent-items-img">
          <img class="lazy" src="/images/square.webp" width="100px" data-src="{{ get_inventory_img_src($item, 'small') }}" data-name="product_image" alt="{{ $item->title }}" title="{{ $item->title }}">
        </div>
      </a>

      @if (empty($title))
        <div class="box-title">
          <a href="{{ route('show.product', $item->slug) }}">
            {{ $item->title }}
          </a>
        </div>
      @endif

      <div class="box-ratting">
        @include('theme::partials._ratings', ['ratings' => $item->ratings])
      </div>

      @if (empty($pricing))
        <div class="box-price mb-2">
          @include('theme::partials._home_pricing')
        </div>
      @endif

      @if ($item->auctionable)
        @include('auction::frontend._place_bid_btn')
      @else
        <a href="javascript:void(0);" data-link="{{ route('cart.addItem', $item->slug) }}" class="btn btn-primary sc-add-to-cart" tabindex="0">
          <i class="fal fa-shopping-cart"></i>
          <span class="d-none d-sm-inline-block ml-2">{{ trans('theme.add_to_cart') }}</span>
        </a>
      @endif
    </div> <!-- /.col -->

    @php
      $t_productSlugs[] = $item->slug;
      $t_totalPrice += $item->current_sale_price();
    @endphp
  @endforeach

  <div class="col">
    <span class="text-secondary mt-3"> @lang('theme.price_for_all') <strong class="text-primary ml-2">{!! get_formated_currency($t_totalPrice, config('system_settings.decimals', 2)) !!}</strong></span>
    </br>
    <a data-link="{{ route('cart.addItem', ['slug' => json_encode($t_productSlugs)]) }}" class="btn btn-sm add-to-card-now-btn sc-add-to-cart mt-2">
      <i class="fal fa-shopping-cart"></i> @lang('theme.button.add_all_to_cart')
    </a>
  </div>
</div> <!-- /.row -->
