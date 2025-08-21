<div class="product-info-price">
  <span class="product-info-price-new">
    {!! get_formated_currency($item->current_sale_price(), config('system_settings.decimals', 2)) !!}
  </span>

  @if ($item->hasOffer())
    <span class="old-price">{!! get_formated_currency($item->sale_price, config('system_settings.decimals', 2)) !!}</span>
  @endif
</div>
