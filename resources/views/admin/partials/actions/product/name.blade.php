{{ $product->name }}

@unless ($product->active)
  <span class="label label-default indent10">{{ trans('app.inactive') }}</span>
@endunless

@if(is_incevio_package_loaded('shopify') && $product->isFromShopify())
  <span class="label label-success cursor pull-right mx-2" data-toggle="tooltip" data-placement="top" title="{{ trans('packages.shopify.imported_from_shopify') }}">
    <i class="fa fa-shopping-bag"></i> {{ trans('packages.shopify.shopify') }}
  </span>
@endif

@if (is_incevio_package_loaded('inspector') && $product->inspection_status && $product->inInspection())
  <br />
  {!! trans('packages.inspector.inspection') . ': ' . $product->getInspectionStatus() !!}
@endif
