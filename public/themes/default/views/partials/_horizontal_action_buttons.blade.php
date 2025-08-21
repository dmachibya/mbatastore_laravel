<a class="button product-link itemQuickView" href="javascript:void(0);" data-link="{{ route('quickView.product', $item->slug) }}" rel="nofollow noindex" data-toggle="tooltip" data-placement="top" title="{{ trans('app.quick_view') }}">
  <i class="fal fa-eye"></i>
</a>

@if (is_incevio_package_loaded('comparison'))
  <a href="javascript:void(0);" class="button product-link add-to-product-compare" data-link="{{ route('comparable.add', $item->id) }}" data-toggle="tooltip" data-placement="top" title="{{ trans('packages.product_comparison.add_to_compare') }}">
    <i class="fal fa-balance-scale"></i>
  </a>
@endif

<a href="javascript:void(0);" data-link="{{ route('wishlist.add', $item) }}" class="button add-to-wishlist" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.button.add_to_wishlist') }}">
  <i class="fal fa-heart"></i>
</a>

<a href="javascript:void(0);" data-link="{{ route('cart.addItem', $item->slug) }}" class="button button-cart btn-primary sc-add-to-cart px-4" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.button.add_to_cart') }}">
  <i class="fal fa-shopping-cart"></i>
  <span class="d-sm-none">{{ trans('theme.add_to_cart') }}</span>
</a>
