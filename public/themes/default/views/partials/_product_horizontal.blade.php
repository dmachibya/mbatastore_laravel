@foreach ($products as $item)
  <div class="items-slider box border-animate">
    <div class="box-inner">
      <a href="{{ route('show.product', $item->slug) }}">
        <div class="recent-items-img box-img">
          <img class="lazy" src="/images/square.webp" data-src="{{ get_inventory_img_src($item, 'medium') }}" data-name="product_image" alt="{{ $item->title }}" title="{{ $item->title }}">
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

      {{-- @if (empty($hover)) --}}
      <div class="box-action">
        <div class="box-action-price my-2">
          @include('theme::partials._home_pricing')
        </div>

        @include('theme::partials._btn_add_to_cart')
      </div>
      {{-- @endif --}}
    </div> <!-- /.box-inner -->
  </div> <!-- /.box -->
@endforeach
