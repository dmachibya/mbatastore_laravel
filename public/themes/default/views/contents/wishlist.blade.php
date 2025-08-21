@if ($wishlist->count() > 0)
  <h4 class="title my-4">@lang('theme.wishlist')</h4>

  <div class="row product-list-wrapper">
    @foreach ($wishlist as $wish)
      <div class="col-md-12 mb-2 mb-sm-0">
        <div class="product product-list-view radius border">
          <ul class="product-info-labels">
            @if ($wish->inventory->free_shipping == 1)
              <li>@lang('theme.free_shipping')</li>
            @endif

            @if ($wish->inventory->stuff_pick == 1)
              <li>@lang('theme.stuff_pick')</li>
            @endif

            @if ($wish->inventory->hasOffer())
              <li>@lang('theme.percent_off', ['value' => get_percentage_of($wish->inventory->sale_price, $wish->inventory->offer_price)])</li>
            @endif
          </ul>

          <div class="product-img-wrap border-r">
            <img class="lazy product-img-primary" src="{{ get_product_img_src($wish->inventory, 'tiny_thumb') }}" data-src="{{ get_product_img_src($wish->inventory, 'full') }}" alt="{{ $wish->inventory->title }}" title="{{ $wish->inventory->title }}" />

            <img class="lazy product-img-alt" src="{{ get_product_img_src($wish->inventory, 'tiny_thumb', 'alt') }}" data-src="{{ get_product_img_src($wish->inventory, 'full', 'alt') }}" alt="{{ $wish->inventory->title }}" title="{{ $wish->inventory->title }}" />

            <a class="product-link" href="{{ route('show.product', $wish->inventory->slug) }}"></a>
          </div>

          <div class="product-actions">
            <a class="btn btn-default itemQuickView" href="javascript:void(0);" data-link="{{ route('quickView.product', $wish->inventory->slug) }}" rel="nofollow noindex">
              <i class="far fa-eye" data-toggle="tooltip" title="@lang('theme.button.quick_view')"></i>
              <span class="ml-1">@lang('theme.button.quick_view')</span>
            </a>

            <a class="btn btn-primary sc-add-to-cart add-to-card-mod" data-link="{{ route('cart.addItem', $wish->inventory->slug) }}" data-toggle="tooltip" title="@lang('theme.add_to_cart')">
              <i class="far fa-shopping-cart"></i> <span class="ml-1">@lang('theme.button.add_to_cart')</span>
            </a>

            <a class="btn btn-primary" href="{{ route('direct.checkout', $wish->inventory->slug) }}">
              <i class="fas fa-rocket mr-1"></i> <span class="ml-1">@lang('theme.button.buy_now')</span>
            </a>

            {!! Form::open(['route' => ['wishlist.remove', $wish], 'method' => 'delete', 'class' => 'data-form']) !!}
            <button class="btn btn-link btn-block confirm" type="submit">
              <i class="fas fa-trash-alt" data-toggle="tooltip" title="@lang('theme.button.remove_from_wishlist')"></i>
              <span class="ml-1">@lang('theme.button.remove')</span>
            </button>
            {!! Form::close() !!}
          </div>

          <div class="product-info">
            @include('theme::layouts.ratings', ['ratings' => $wish->inventory->ratings])

            <a href="{{ route('show.product', $wish->inventory->slug) }}" class="product-info-title">
              {{ $wish->inventory->title }}
            </a>

            <div class="product-info-availability">
              @lang('theme.availability'):
              <span>{{ $wish->inventory->stock_quantity > 0 ? trans('theme.in_stock') : trans('theme.out_of_stock') }}</span>
            </div>

            @include('theme::layouts.pricing', ['item' => $wish->inventory])

            <div class="product-info-desc"> {!! $wish->inventory->description !!} </div>
            <ul class="product-info-feature-list">
              <li>{{ $wish->inventory->condition }}</li>
            </ul>
          </div><!-- /.product-info -->
        </div><!-- /.product -->
      </div><!-- /.col-md-* -->
    @endforeach
  </div><!-- /.row .product-list-wrapper -->
  <hr class="dotted" />
@else
  <p class="lead text-center border mb-5 p-5">
    @lang('theme.empty_wishlist')
    <br />
    <a href="{{ url('/') }}" class="btn btn-primary btn-sm">@lang('theme.button.shop_now')</a>
  </p>
@endif

<div class="row pagenav-wrapper mb-3">
  {{ $wishlist->links('theme::layouts.pagination') }}
</div><!-- /.row .pagenav-wrapper -->

<script>
  // Add-to-wishlist
  $(".add-to-wishlist").off().on("click", function(e) {
    e.preventDefault();

    $.ajax({
      url: $(this).data('link'),
      type: 'get',
      complete: function(xhr, textStatus) {
        if (200 == xhr.status) {
          @include('theme::layouts.notification', ['message' => trans('theme.item_added_to_wishlist'), 'type' => 'success', 'icon' => 'check-circle'])
        } else if (401 == xhr.status) {
          location.href = '{{ route('customer.login') }}';
        } else if (404 == xhr.status) {
          @include('theme::layouts.notification', ['message' => trans('theme.item_not_available'), 'type' => 'warning', 'icon' => 'info-circle'])
        } else {
          @include('theme::layouts.notification', ['message' => trans('theme.notify.failed'), 'type' => 'warning', 'icon' => 'times-circle'])
        }
      },
    });
  });
</script>
