<p class="feature-items-price-new box-price-new">
  {!! get_formated_currency($item->current_sale_price(), config('system_settings.decimals', 2)) !!}
</p>

@if ($item->hasOffer())
  <p class="feature-items-price-old box-price-old">
    {!! get_formated_currency($item->sale_price, config('system_settings.decimals', 2)) !!}
  </p>
@endif
