<div class="main-menu mobile-mega-menu">
  <nav>
    <div class="main-menu-top pt-0">
      <div class="main-menu-top-inner">
        <div class="main-menu-top-box">
          <!-- <div class="main-menu-top-item"><a href="#"><i class="fal fa-user"></i></a></div> -->
          @auth('customer')
            <div class="main-menu-top-item">
              <a href="{{ route('account', 'dashboard') }}" class="text-center">
                <i class="fal fa-user small"></i>
                <p class="small">{{ trans('theme.account') }}</p>
              </a>
            </div>

            <div class="main-menu-top-item">
              <a href="{{ route('customer.logout') }}" class="text-center">
                <i class="fal fa-sign-out-alt"></i>
                <p class="small">{{ trans('theme.logout') }}</p>
              </a>
            </div>
          @else
            <div class="main-menu-top-item">
              <a href="{{ route('account', 'dashboard') }}" class="text-center">
                <i class="fal fa-sign-in-alt small"></i>
                <p>{{ trans('theme.login') }}</p>
              </a>
            </div>
            @endif

            <!-- <div class="main-menu-top-item"><a href="#"><i class="fas fa-wallet"></i></a></div> -->
            <div class="main-menu-top-item">
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

      @if (is_null($hidden_menu_items) || !in_array('Categories', $hidden_menu_items))
        <ul class="main-menu-nav">
          @foreach ($all_categories as $catGroup)
            @if ($catGroup->subGroups->count())
              @php
                $categories_count = $catGroup->subGroups->sum('categories_count');
              @endphp
              <li class="flex-center-y">
                <a href="{{ route('categoryGrp.browse', $catGroup->slug) }}">{{ $catGroup->name }}</a>
                <ul>
                  @foreach ($catGroup->subGroups as $subGroup)
                    <li>
                      <a href="{{ route('categories.browse', $subGroup->slug) }}">{{ $subGroup->name }}</a>
                      <ul>
                        @foreach ($subGroup->categories as $cat)
                          <li>
                            <a href="{{ route('category.browse', $cat->slug) }}">{{ $cat->name }}</a>
                          </li>
                        @endforeach
                      </ul>
                    </li>
                  @endforeach
                </ul>
              </li>
            @endif
          @endforeach
        </ul>
      @endif
    </nav>
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
