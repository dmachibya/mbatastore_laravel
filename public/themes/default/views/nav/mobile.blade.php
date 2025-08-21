<div class="main-menu mobile-mega-menu">
  <nav class="bg-white">
    @if (is_null($hidden_menu_items) || !in_array('Categories', $hidden_menu_items))
      <ul class="main-menu-nav bg-white">
        @foreach ($all_categories as $catGroup)
          @php
            $categories_count = $catGroup->subGroups->sum('categories_count');
          @endphp

          <li class="flex-center-y">
            <a href="{{ route('categoryGrp.browse', $catGroup->slug) }}">{{ $catGroup->name }}</a>
            <ul>
              @foreach ($catGroup->subGroups as $subGroup)
                <li>
                  <a href="{{ route('categories.browse', $subGroup->slug) }}">{{ $subGroup->name }}</a>
                  {{-- 
                        there were 2 ul tags
                       --}}
                  <ul class="child-categories">
                    {{-- <ul class="under-child-categories"> --}}
                    @foreach ($subGroup->categories as $cat)
                      <li>
                        <a href="{{ route('category.browse', $cat->slug) }}">{{ $cat->name }}</a>
                      </li>
                    @endforeach
                    {{-- </ul> --}}
                  </ul>
                </li>
              @endforeach
            </ul>
          </li>
        @endforeach
      </ul>
    @endif
  </nav>
</div>

<div class="main-menu-top">
  <div class="main-menu-top-inner">
    <div class="main-menu-top-box flex-wrap flex-center">
      @auth('customer')
        <div class="main-menu-top-item mm-acount">
          <a href="{{ route('account', 'dashboard') }}" class="flex-center-y">
            <i class="fa fa-user"></i>
            <p>{{ Str::upper(trans('theme.account')) }}</p>
          </a>
        </div>
        <div class="main-menu-top-item mm-log">
          <a href="{{ route('customer.logout') }}" class="flex-center-y">
            <i class="fa fa-sign-out"></i>
            <p>{{ Str::upper(trans('theme.logout')) }}</p>
          </a>
        </div>
      @else
        <div class="main-menu-top-item mm-log">
          <a href="{{ route('account', 'dashboard') }}" class="flex-center-y">
            <i class="fa fa-sign-in"></i>
            <p>{{ Str::upper(trans('theme.login')) }}</p>
          </a>
        </div>
        @endif
        <div class="main-menu-top-item mm-lang">
          <div class="form-group">
            <select name="lang" id="mobile-lang">
              @foreach (config('active_locales') as $lang)
                <option dd-link="{{ route('locale.change', $lang->code) }}" value="{{ $lang->code }}" data-imagesrc="{{ get_flag_img_by_code(array_slice(explode('_', $lang->php_locale_code), -1)[0], true) }}" {{ $lang->code == \App::getLocale() ? 'selected' : '' }}>
                  {{ $lang->code }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        @if (is_incevio_package_loaded('dynamic-currency'))
          <li class="currency">
            <select name="currency" id="currencyChange">
              @foreach (get_active_currencies() as $item)
                @php
                  if (get_dynamic_currency_attr('iso_code') == $item->iso_code) {
                      $selected = 'selected';
                  } elseif (!session()->has('currency') && $item->iso_code == get_system_currency()) {
                      $selected = 'selected';
                  } else {
                      $selected = '';
                  }
                @endphp
                <option value="{{ $item->iso_code }}" {{ $selected ?? '' }}>
                  {{ $item->iso_code ?? '' }} ({{ $item->symbol ?? '' }})
                </option>
              @endforeach
            </select>
          </li>
        @endif
      </div>
    </div>
  </div>
  <div class="main-menu-bottom">
    <div class="main-menu-bottom-inner">
      <div class="main-menu-bottom-box">
        <div class="main-menu-bottom-item">
          <a href="{{ route('account', 'wishlist') }}">
            <i class="fal fa-heart small"></i> <span>{{ trans('theme.wishlist') }}</span>
          </a>
        </div>

        @if (is_null($hidden_menu_items) || !in_array('Brands', $hidden_menu_items))
          <div class="main-menu-bottom-item">
            <a href="{{ route('brands') }}">
              <i class="fal fa-crown small"></i> <span>{{ trans('theme.brands') }}</span>
            </a>
          </div>
        @endif

        @if (is_null($hidden_menu_items) || !in_array('Vendors', $hidden_menu_items))
          <div class="main-menu-bottom-item">
            <a href="{{ route('shops') }}">
              <i class="fal fa-store small"></i> <span>{{ trans('theme.vendors') }}</span>
            </a>
          </div>
        @endif

        @if (is_null($hidden_menu_items) || !in_array('Sale', $hidden_menu_items))
          <div class="main-menu-bottom-item">
            <a href="{{ url('/selling') }}">
              <i class="fal fa-seedling small"></i> <span>{{ trans('theme.sell') }}</span>
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
