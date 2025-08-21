<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">
    <a class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
    <div class="row sc-product-item">
      <div class="col-md-5 col-sm-6 pl-1 pt-1">
        @include('theme::layouts.jqzoom', ['item' => $item])
      </div>

      <div class="col-md-7 col-sm-6">
        <div class="product-single mb-5">
          @include('theme::partials._product_info', ['zoomID' => 'quickViewZoom', 'item' => $item])

          <hr class="dotted" />

          <div class="row product-attribute">
            @if (is_incevio_package_loaded('wholesale') && !$item->wholesale_prices->isEmpty())
              <div class="col-5 pr-0">
                @include('wholesale::quickview_price_table')
              </div>
            @endif

            <div class="{{ is_incevio_package_loaded('wholesale') && !$item->wholesale_prices->isEmpty() ? 'col-7' : 'col-12' }} pr-0">
              @if ($item->key_features)
                <div class="section-title">
                  <h4 class="px-0">{!! trans('theme.section_headings.key_features') !!}</h4>
                </div>

                <ul class="key-feature-list">
                  @foreach (unserialize($item->key_features) as $key_feature)
                    <li>
                      <i class="fal fa-check-double"></i>
                      <span>{{ $key_feature }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif

              <a href="{{ route('show.product', $item->slug) }}" class="btn btn-default rounded mt-3 ml-3">
                @lang('theme.button.view_product_details')
              </a>
            </div><!-- /.col-sm-9 .col-6 -->
          </div><!-- /.row -->

          <hr class="dotted my-4" />

          <a href="javascript:void(0);" data-link="{{ route('cart.addItem', $item->slug) }}" class="btn btn-primary rounded px-4 py-2 sc-add-to-cart" data-dismiss="modal">
            <i class="fas fa-shopping-bag mr-2"></i>
            @lang('theme.button.add_to_cart')
          </a>

          <a href="{{ route('direct.checkout', $item->slug) }}" class="btn btn-primary rounded px-5 py-2" id="buy-now-btn">
            <i class="fas fa-rocket mr-2"></i>
            @lang('theme.button.buy_now')
          </a>

          @if ($item->product->inventories_count > 1)
            <a href="{{ route('show.offers', $item->product->slug) }}" class="btn btn-sm btn-link">
              @lang('theme.view_more_offers', ['count' => $item->product->inventories_count])
            </a>
          @endif
        </div><!-- /.product-single -->
      </div>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
