<button type="button" id="filterBtn">
  <span class="sr-only">{{ trans('theme.filters') }}</span>
  <i class="fas fa-filter"></i>
</button>

<aside class="category-filters mb-4">
  <h3>
    <i class="fa fa-sliders"></i> {{ trans('theme.filters') }}
    <a href="javascript::void(0)" data-name="all" class="clear-filter small pull-right">
      <i class="fa fa-times"></i> @lang('theme.clear_all_filters')
    </a>
  </h3>

  @include('theme::partials._categories_filter')

  {{-- Condition --}}
  @if (config('system_settings.show_item_conditions'))
    <div class="category-filters-section">
      <h3>
        {{ trans('theme.condition') }}
      </h3>

      <div class="checkbox">
        <label>
          <input name="condition[New]" class="i-check filter_opt_checkbox" type="checkbox" {{ Request::has('condition.New') ? 'checked' : '' }}>
          @lang('theme.new')
        </label>
      </div>

      <div class="checkbox">
        <label>
          <input name="condition[Used]" class="i-check filter_opt_checkbox" type="checkbox" {{ Request::has('condition.Used') ? 'checked' : '' }}>
          @lang('theme.used')
        </label>
      </div>

      <div class="checkbox">
        <label>
          <input name="condition[Refurbished]" class="i-check filter_opt_checkbox" type="checkbox" {{ Request::has('condition.Refurbished') ? 'checked' : '' }}>
          @lang('theme.refurbished')
        </label>
      </div>
    </div>
  @endif

  {{-- Rating --}}
  @unless (Request::is('search*'))
    <div class="category-filters-section">
      <h3>
        {{ trans('theme.rating') }}

        @if (Request::has('rating'))
          <a href="javascript::void(0)" data-name="rating" class="clear-filter small text-lowercase pull-right">@lang('theme.button.clear')</a>
        @endif
      </h3>

      <ul class="cateogry-filters-list">
        @for ($i = 4; $i > 0; $i--)
          <li>
            <a href="javascript::void(0)" data-name="rating" data-value="{{ $i }}" class="link-filter-opt product-info-rating">
              @for ($j = 0; $j < 5; $j++)
                @if ($j < $i)
                  <span class="rated">
                    <i class="fas fa-star"></i>
                  </span>
                @else
                  <span>
                    <i class="far fa-star"></i>
                  </span>
                @endif
              @endfor

              <span class="small {{ Request::get('rating') == $i ? 'active' : '' }}">
                &amp; @lang('theme.up')
              </span>
            </a>
          </li>
        @endfor
      </ul>
    </div>
  @endunless

  {{-- Price --}}
  @php
    $priceRange = $priceRange ?? get_price_ranges_from_listings($products);
  @endphp
  @if ($priceRange['max'] - $priceRange['min'] > 0)
    <div class="category-filters-section">
      <h3>
        {{ trans('theme.price') }}

        @if (Request::has('price'))
          <a href="javascript::void(0)" data-name="price" class="clear-filter small text-lowercase pull-right">@lang('theme.button.clear')</a>
        @endif
      </h3>

      <ul class="cateogry-filters-list mb-3">
        @foreach (generate_ranges($priceRange['min'], $priceRange['max'], 5) as $ranges)
          <li>
            <a href="javascript::void(0)" data-name="price" data-value="{{ $ranges['lower'] . '-' . $ranges['upper'] }}" class="link-filter-opt {{ Request::get('price') == $ranges['lower'] . '-' . $ranges['upper'] ? 'active' : '' }}">
              @if ($loop->first)
                {{ trans('theme.price_under', ['value' => get_formated_currency($ranges['upper'])]) }}
              @elseif($loop->last)
                {{ trans('theme.price_above', ['value' => get_formated_currency($ranges['lower'])]) }}
              @else
                <span class="text-lowercase">
                  {{ get_formated_currency($ranges['lower']) . ' ' . trans('theme.to') . ' ' . get_formated_currency($ranges['upper']) }}
                </span>
              @endif
            </a>
          </li>
        @endforeach
      </ul>
      <input type="text" id="price-slider" />
    </div>
  @endif

  {{-- Attribute --}}
  @if (isset($category->attrsList))
    @foreach ($category->attrsList as $attribute)
      <div class="category-filters-section">
        <h3>
          {{ $attribute->name }}

          @if ($attribute->attributeValues->first() && Request::has('attribute.' . $attribute->attributeValues->first()->id))
            <a href="javascript::void(0)" data-name="attribute[{{ $attribute->attributeValues->first()->id }}]" class="clear-filter small text-lowercase pull-right">@lang('theme.button.clear')</a>
          @endif
        </h3>

        @foreach ($attribute->attributeValues as $attributeValue)
          <div class="checkbox">
            <label>
              <input type="checkbox" value="{{ $attribute->id }}" name="attribute[{{ $attributeValue->id }}]" class="i-check filter_opt_checkbox" {{ Request::has('attribute.' . $attributeValue->id) ? 'checked' : '' }}> {{ \Str::title(\Str::limit($attributeValue->value, 30)) }}
            </label>
          </div>
        @endforeach
      </div>
    @endforeach
  @endif

  {{-- Brand --}}
  @php
    $brands = $brands ?? \App\Helpers\ListHelper::get_unique_brand_names_from_listings($products);
  @endphp
  @if (count($brands))
    <div class="category-filters-section">
      <h3>
        {{ trans('theme.brand') }}
      </h3>

      @foreach ($brands as $brand)
        <div class="checkbox">
          <label>
            <input name="brand[{{ str_replace(' ', '%20', $brand) }}]" class="i-check filter_opt_checkbox" type="checkbox" {{ Request::has('brand.' . $brand) ? 'checked' : '' }}> {{ \Str::title(\Str::limit($brand, 30)) }}
          </label>
        </div>
      @endforeach
    </div>
  @endif

</aside>
