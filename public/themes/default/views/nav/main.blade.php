<div id="primary-navigation">
  <div class="top-nav">
    <div class="container">
      <div class="top-nav-inner">
        <div class="top-nav-left">
          {{-- @if (is_incevio_package_loaded('zipcode') && Session::has('zipcode')) --}}
          @if (is_incevio_package_loaded('zipcode') && Session::has('zipcode_default'))
            <a class="modalAction" href="{{ route(config('zipcode.routes.shipTo')) }}">
              <i class="fal fa-location-arrow"></i> {{ trans('theme.ship_to') . ' ' . Session::get('zipcode_default') }}
            </a>
          @else
            <h3>{{ trans('theme.welcome') . ' ' . config('app.name') }}</h3>
          @endif

          {{-- @unless (empty($promotional_tagline['text']))
            <a style="text-decoration: none" href="{{ $promotional_tagline['action_url'] ?? 'javascript:void(0)' }}">
              {!! $promotional_tagline['text'] !!}
            </a>
          @endunless --}}
        </div> <!-- /.top-nav-left -->

        <div class="top-nav-right">
          <ul>
            @auth('customer')
              <li class="image-icon">
                <a href="{{ route('account', 'dashboard') }}">
                  <i class="fal fa-user"></i>
                  <span>{{ trans('theme.hello') . ', ' . Auth::guard('customer')->user()->getName() }}</span>
                </a>
              </li>

              @if (customer_can_register())
                <li class="image-icon">
                  <a href="{{ route('customer.logout') }}">
                    <i class="fal fa-power-off"></i>
                    <span>{{ trans('theme.logout') }}</span>
                  </a>
                </li>
              @else
                <li class="image-icon">
                  <a href="{{ route('customer.switchToMerchant') }}">
                    <i class="fal fa-dashboard"></i> {{ trans('theme.view_merchant_dashboard') }}
                  </a>
                </li>
              @endif
            @else
              @if (customer_can_register())
                <li class="image-icon">
                  <a href="javascript:void(0);" data-toggle="modal" data-target="#loginModal">
                    <i class="fal fa-user"></i>
                    <span>{{ trans('theme.sing_in') }}</span>
                  </a>
                </li>
              @else
                <li class="image-icon">
                  <a href="/login">
                    <i class="fal fa-user"></i>
                    <span>{{ trans('theme.sing_in') }}</span>
                  </a>
                </li>
              @endif
            @endauth

            @if (is_wallet_configured_for('customer'))
              <li class="image-icon">
                <a href="{{ route('customer.account.wallet') }}">
                  <i class="fas fa-wallet no-fill"></i>
                  @if (Auth::guard('customer')->check())
                    <strong>{{ get_formated_currency(Auth::guard('customer')->user()->balance) }}</strong>
                  @else
                    {{ trans('packages.wallet.wallet') }}
                  @endif
                </a>
              </li>
            @endif

            {{-- <li class="image-icon">
              <a href="{{ route('brands') }}">
                <i class="fal fa-crown"></i> {{ trans('theme.all_brands') }}
              </a>
            </li>

            <li class="image-icon">
              <a href="{{ route('shops') }}">
                <i class="fal fa-store"></i> {{ trans('theme.all_shops') }}
              </a>
            </li> --}}

            <li class="image-icon">
              <a href="{{ route('account', 'orders') }}">
                <!-- <img src="images/truck.svg" alt=""> -->
                <i class="fal fa-map-marker-alt"></i> {{ trans('theme.track_your_order') }}
              </a>
            </li>

            <li class="image-icon">
              <a href="{{ get_page_url(\App\Models\Page::PAGE_CONTACT_US) }}">
                <i class="fal fa-life-ring"></i> {{ trans('theme.support') }}
              </a>
            </li>

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

            <li class="language">
              <select id="languageChange" name="lang" class="dd-dropdown">
                @foreach (config('active_locales') as $lang)
                  <option dd-link="{{ route('locale.change', $lang->code) }}" value="{{ $lang->code }}" data-imagesrc="{{ get_flag_img_by_code(array_slice(explode('_', $lang->php_locale_code), -1)[0], true) }}" {{ $lang->code == \App::getLocale() ? 'selected' : '' }}>
                    {{ $lang->language }}
                  </option>
                @endforeach
              </select>
            </li> <!-- /.language -->
          </ul>
        </div> <!-- /.top-nav-right -->
      </div> <!-- /.top-nav-inner -->
    </div> <!-- /.container -->
  </div> <!-- /.top-nav -->

  <div id="header-main">
    <div class="container">
      <div class="row align-items-center align-items-lg-start justify-content-lg-between">
        <div class="col-7 col-lg-2 d-flex align-items-center justify-content-sm-between">
          <div class="header-menu-toggler">
            <div class="menu-icon">
              <a class="main-menu-toggle" href="javascript:void(0);" aria-label="main menu toggle button"><i class="fal fa-bars"></i></a>
            </div>
          </div> <!-- /.header-menu-toggler -->

          <div class="header-logo">
            <a href="{{ url('/') }}">
              <img src="{{ get_logo_url('system', 'logo') }}" class="brand-logo" alt="{{ trans('theme.logo') }}" title="{{ trans('theme.logo') }}">
            </a>
          </div> <!-- /.header-logo -->
        </div> <!-- /.col-* -->

        <div class="col-lg-8 global-search">
          <div class="header-search d-flex ml-md-4 align-items-center">
            <span class="exit-search flex-center fal fa-times"></span>
            {!! Form::open(['route' => 'inCategoriesSearch', 'method' => 'GET', 'id' => 'search-categories-form', 'class' => 'navbar-left navbar-search w-100', 'role' => 'search']) !!}
            <div class="search-box">
              <div class="search-box-select d-none d-sm-block">
                <select class="category search-category-select" name="insubgrp" id="niceSelect">
                  <option value="all">{{ trans('theme.all_categories') }}</option>

                  @foreach ($search_category_list as $search_category_grp)
                    <optgroup label="{{ $search_category_grp->name }}">
                      @foreach ($search_category_grp->subGroups as $search_category)
                        <option value="{{ $search_category->slug }}" {{ Request::get('insubgrp') == $search_category->slug ? 'selected' : '' }}>
                          {{ $search_category->name }}
                        </option>
                      @endforeach
                    </optgroup>
                  @endforeach
                </select> <!-- /.category -->
              </div> <!-- /.search-box-select -->

              <div class="search-box-input">
                {!! Form::text('q', Request::get('q'), ['id' => 'autoSearchInput', 'placeholder' => trans('theme.main_searchbox_placeholder'), 'autocomplete' => 'off', 'data-search']) !!}
              </div>

              <div class="search-box-button">
                <button type="submit" class="navbar-search-submit">
                  <i class="fal fa-search"></i>
                </button>
              </div>

              {{-- Search Autocomplete package load --}}
              @if (is_incevio_package_loaded('searchAutocomplete'))
                @include('searchAutocomplete::_autoComplete')
              @endif
            </div> <!-- /.search-box -->
            {!! Form::close() !!}

            <p id="search-nav-feedabck" class="pl-4 text-danger small hide">
              {{ trans('theme.type_min_char', ['min' => 3]) }}
            </p>

            {{-- Trending Keywords --}}
            @if (is_incevio_package_loaded('trendingKeywords'))
              <div class="text-center trending-words">
                @include('trendingKeywords::_keyword_lists')
              </div>
            @endif
          </div> <!-- /.header-search -->
        </div> <!-- /.global-search -->

        <div class="col-5 col-lg-2 mt-lg-2">
          <div class="header-utility d-flex h-100 align-items-center">
            <ul class="d-flex">
              <li class="search-btn">
                <span class="fal fa-search"></span>
              </li>

              <li>
                <a href="{{ route('account', 'account') }}" aria-label="{{ trans('theme.your_account') }}">
                  <i class="fal fa-user" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.your_account') }}"></i>
                  <!-- <img src="images/big-user.svg" alt=""> -->
                </a>
              </li>

              @if (is_incevio_package_loaded('comparison'))
                @php
                  $comparison_item = !empty(Session::get('comparables')) ? count(Session::get('comparables')) : 0;
                @endphp

                <li>
                  <a href="{{ route('product.comparables') }}" aria-label="{{ trans('theme.your_comparisons') }}">
                    <i class="fal fa-balance-scale" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.your_comparisons') }}"></i>
                    <span id="globalCompareItemCount" class="badge {{ $comparison_item == 0 ? 'hidden' : '' }}">{{ $comparison_item }}</span>
                  </a>
                </li>
              @endif

              <li>
                <a href="{{ route('account', 'wishlist') }}" aria-label="{{ trans('theme.your_wishlist') }}">
                  <i class="fal fa-heart" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.your_wishlist') }}"></i>
                  <span id="globalWishlistItemCount" class="badge {{ $wishlist_item_count == 0 ? 'hidden' : '' }}">{{ $wishlist_item_count }}</span>
                </a>
              </li>

              <li>
                <a href="{{ route('cart.index') }}" aria-label="{{ trans('theme.your_cart') }}">
                  <i class="fal fa-shopping-cart" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.your_cart') }}"></i>
                  <span id="globalCartItemCount" class="badge {{ $cart_item_count == 0 ? 'hidden' : '' }}">{{ $cart_item_count }}</span>
                </a>
              </li>
            </ul>
          </div> <!-- /.header-utility -->
        </div> <!-- /.col-3 -->
      </div> <!-- /.row -->
    </div> <!-- /.container -->
  </div> <!-- /.header-main -->
</div> <!-- /.primary-navigation -->

<div class="primary-nav">
  <div class="container" id="primary-nav-container">
    <div class="primary-nav-inner">
      <ul class="menu-dropdown-list primary-nav-category">
        @if (is_null($hidden_menu_items) || !in_array('Categories', $hidden_menu_items))
          <li>
            <a href="{{ route('categories') }}" class="menu-link" data-menu-link aria-label="{{ trans('theme.categories') }}">
              <i class="fas fa-bars mr-2"></i>
              {{ trans('theme.categories') }}
            </a>

            <ul class="menu-cat hide">
              <div class="menu-list-wrapper" data-simplebar>
                <div class="menu-list">
                  @foreach ($all_categories as $catGroup)
                    <li data-category-id="{{ $catGroup->id }}">
                      <a href="{{ route('categoryGrp.browse', $catGroup->slug) }}" aria-label="{{ $catGroup->name }}">
                        @if ($catGroup->logoImage && Storage::exists($catGroup->logoImage->path))
                          <img src="{{ get_storage_file_url($catGroup->logoImage->path, 'tiny_thumb') }}" alt="{{ $catGroup->name }}">
                        @else
                          <i class="fal {{ $catGroup->icon ?? 'fa-cube' }} mb-0"></i>
                        @endif
                        <span>{{ $catGroup->name }}</span>
                        <i class="fal fa-chevron-right mb-0"></i>
                      </a>
                    </li>
                  @endforeach
                </div>
              </div>

              @foreach ($all_categories as $catGroup)
                <div class="mega-dropdown common-dropdown" data-simplebar data-category-id="{{ $catGroup->id }}" @if ($catGroup->backgroundImage) style="background-image: url('{{ get_storage_file_url(optional($catGroup->backgroundImage)->path, 'full') }}')" @endif>
                  <div class="row">
                    @foreach ($catGroup->subGroups as $subGroup)
                      <div class="col-lg-4 col-xl-3 mb-2">
                        <div class="mega-dropdown-item">
                          <h3 class="mb-1">
                            <a href="{{ route('categories.browse', $subGroup->slug) }}" aria-label="{{ $subGroup->name }}">{{ $subGroup->name }}</a>
                          </h3>

                          <ul>
                            @foreach ($subGroup->categories as $cat)
                              <li>
                                <a href="{{ route('category.browse', $cat->slug) }}" aria-label="{{ $cat->name }}">{{ $cat->name }}</a>
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    @endforeach
                  </div> <!-- ./row -->
                </div> <!-- ./mega-dropdown -->
              @endforeach
            </ul>
          </li>
        @endif
      </ul>

      <ul class="header-nav-items">
        @if (is_null($hidden_menu_items) || !in_array('Brands', $hidden_menu_items))
          <li>
            <a class="menu-link" href="{{ route('brands') }}">
              <i class="fal fa-crown menu-icon"></i> {{ trans('theme.brands') }}
            </a>
          </li>
        @endif

        @if (is_null($hidden_menu_items) || !in_array('Vendors', $hidden_menu_items))
          <li>
            <a class="menu-link" href="{{ route('shops') }}">
              <i class="fal fa-store menu-icon"></i> {{ trans('theme.vendors') }}
            </a>
          </li>
        @endif

        @if (is_incevio_package_loaded('auction'))
          @if (is_null($hidden_menu_items) || !in_array('auction', $hidden_menu_items))
            <li>
              <a class="menu-link" href="{{ route('auctions') }}">
                <i class="fal fa-gavel menu-icon"></i> {{ trans('packages.auction.auctions') }}
              </a>
            </li>
          @endif
        @endif

        @foreach (get_main_nav_categories() as $nav_cat)
          <li>
            <a class="menu-link" href="{{ route('category.browse', $nav_cat->slug) }}">
              <i class="fas fa-fire-alt menu-icon"></i> {{ $nav_cat->name }}
            </a>
          </li>
        @endforeach

        @if (is_incevio_package_loaded('eventy'))
          @if (is_null($hidden_menu_items) || !in_array('events', $hidden_menu_items))
            <li>
              <a class="menu-link" href="{{ route('events') }}">
                <i class="fal fa-calendar-alt menu-icon"></i> {{ trans('theme.events') }}
              </a>
            </li>
          @endif
        @endif

        @foreach ($pages->where('position', 'main_nav') as $page)
          <li>
            <a href="{{ get_page_url($page->slug) }}" class="menu-link">
              <i class="fal fa-link menu-icon"></i> {{ $page->title }}
            </a>
          </li>
        @endforeach

        @if (is_null($hidden_menu_items) || !in_array('Sale', $hidden_menu_items))
          <li>
            <a class="menu-link" href="{{ url('/selling') }}">
              <i class="fal fa-seedling menu-icon"></i>
              {{ trans('theme.nav.sell_on', ['platform' => get_platform_title()]) }}
            </a>
          </li>
        @endif
      </ul>

      @isset($promotional_tagline['action_url'])
        <div class="shale-text">
          <a style="text-decoration: none" href="{{ $promotional_tagline['action_url'] ?? 'javascript:void(0)' }}">
            <p>{{ !empty($promotional_tagline['text']) ? $promotional_tagline['text'] : '' }}</p>
          </a>
        </div>
      @endisset
    </div>
  </div>
</div> <!-- /.primary-nav -->
