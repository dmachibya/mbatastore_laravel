@isset($products)
  <div class="row product-list-wrapper mb-4">
    <div class="col-xl-2 col-lg-3 border radius">
      @include('theme::partials._product_list_sidebar_filters')
    </div><!-- /.col-sm-3 -->

    <div class="col-xl-10 col-lg-9">
      <div class="row product-2nd-parent">
        <div class="col-md-12 pr-0">
          <div class="product-list-top-filter border-t radius px-3 mb-4">
            <span>
              @lang('theme.sort_by'):
              <select name="sort_by" class="selectBoxIt" id="filter_opt_sort">
                <option value="best_match">
                  @lang('theme.best_match')
                </option>

                <option value="newest" {{ Request::get('sort_by') == 'newest' ? 'selected' : '' }}>
                  @lang('theme.newest')
                </option>

                <option value="oldest" {{ Request::get('sort_by') == 'oldest' ? 'selected' : '' }}>
                  @lang('theme.oldest')
                </option>

                <option value="price_asc" {{ Request::get('sort_by') == 'price_asc' ? 'selected' : '' }}>
                  @lang('theme.price'): @lang('theme.low_to_high')
                </option>

                <option value="price_desc" {{ Request::get('sort_by') == 'price_desc' ? 'selected' : '' }}>
                  @lang('theme.price'): @lang('theme.high_to_low')
                </option>
              </select> <!-- /.sort_by -->
            </span>

            @if (is_incevio_package_loaded('auction'))
              <div class="checkbox">
                <label>
                  <input name="auction" class="i-check filter_opt_checkbox" type="checkbox" {{ Request::has('auction') ? 'checked' : '' }}>
                  @lang('packages.auction.auction')
                  {{-- <span class="small">({{ $products->where('auctionable', 1)->count() }})</span> --}}
                </label>
              </div> <!-- /.checkbox -->
            @endif

            <div class="checkbox">
              <label>
                <input name="free_shipping" class="i-check filter_opt_checkbox" type="checkbox" {{ Request::has('free_shipping') ? 'checked' : '' }}>
                @lang('theme.free_shipping')
                {{-- <span class="small">({{ $hasFreeShipping }})</span> --}}
                {{-- <span class="small">({{ $products->where('free_shipping', 1)->count() }})</span> --}}
              </label>
            </div> <!-- /.checkbox -->

            <div class="checkbox">
              <label>
                <input name="has_offers" class="i-check filter_opt_checkbox" type="checkbox" {{ Request::has('has_offers') ? 'checked' : '' }} />
                @lang('theme.has_offers')
                {{-- <span class="small">({{ $hasOffers }})</span> --}}
                {{-- <span class="small">({{ $products->where('offer_price', '>', 0)->where('offer_start', '<', \Carbon\Carbon::now())->where('offer_end', '>', \Carbon\Carbon::now())->count() }})</span> --}}
              </label>
            </div> <!-- /.checkbox -->

            <div class="checkbox">
              <label>
                <input name="new_arrivals" class="i-check filter_opt_checkbox" type="checkbox" {{ Request::has('new_arrivals') ? 'checked' : '' }} />
                @lang('theme.new_arrivals')
                <span class="small">
                  {{-- ({{ $newArrivals }}) --}}
                  {{-- ({{ $products->where('created_at', '>', \Carbon\Carbon::now()->subDays(config('system.filter.new_arraival', 7)))->count() }}) --}}
                </span>
              </label>
            </div> <!-- /.checkbox -->

            <span class="pull-right text-muted d-none d-xl-inline-block ">
              <a href="javascript:void(0);" class="viewSwitcher btn btn-primary btn-sm">
                <i class="fas fa-th" data-toggle="tooltip" title="@lang('theme.grid_view')"></i>
              </a>
              <a href="javascript:void(0);" class="viewSwitcher btn btn-default btn-sm">
                <i class="fas fa-list" data-toggle="tooltip" title="@lang('theme.list_view')"></i>
              </a>
            </span>
          </div> <!-- /.product-list-top-filter -->
        </div> <!-- /.col-md-12 -->

        @forelse ($products as $item)
          <div class="col-6 col-sm-4 col-md-3 col-lg-4 col-xl-{{ $colum ?? '3' }} p-0 mb-2 categoryCard">
            <div class="product product-grid-view sc-product-item border radius">
              <ul class="product-info-labels">
                {{-- @if ($item->shop->isVerified() && Route::current()->getName() != 'show.store')
                  <li>@lang('theme.from_verified_seller')</li>
                @endif --}}

                @foreach ($item->getLabels() as $label)
                  <li>{!! $label !!}</li>
                @endforeach
              </ul> <!-- /.product-info-labels -->

              <div class="product-img-wrap">
                <img class="product-img-primary lazy" src="{{ get_storage_file_url(optional($item->image)->path, 'tiny_thumb') }}" data-src="{{ get_storage_file_url(optional($item->image)->path, 'full') }}" alt="{{ $item->title }}" title="{{ $item->title }}" />

                <img class="product-img-alt lazy" src="{{ get_storage_file_url(optional($item->image)->path, 'tiny_thumb', 'alt') }}" data-src="{{ get_storage_file_url(optional($item->image)->path, 'full', 'alt') }}" alt="{{ $item->title }}" title="{{ $item->title }}" />

                <a class="product-link" href="{{ route('show.product', $item->slug) }}"></a>
              </div> <!-- /.product-img-wrap -->

              <div class="product-actions btn-group radius">
                <a class="btn btn-default add-to-wishlist" href="javascript:void(0);" data-link="{{ route('wishlist.add', $item) }}" data-toggle="tooltip" title="@lang('theme.button.add_to_wishlist')" aria-label="@lang('theme.button.add_to_wishlist')">
                  <i class="far fa-heart"></i> <span>@lang('theme.button.add_to_wishlist')</span>
                </a>

                @if (is_incevio_package_loaded('comparison'))
                  @include('comparison::_product_list_compare_btn')
                @endif

                <a class="btn btn-default itemQuickView" href="javascript:void(0);" data-link="{{ route('quickView.product', $item->slug) }}" rel="nofollow noindex" data-toggle="tooltip" title="@lang('theme.button.quick_view')" aria-label="@lang('theme.button.quick_view')">
                  <i class="far fa-eye"></i> <span>@lang('theme.button.quick_view')</span>
                </a>

                @if (is_incevio_package_loaded('auction') && $item->auctionable)
                  <a class="btn btn-primary" href="{{ route('show.product', $item->slug) }}" data-toggle="tooltip" title="{{ trans('packages.auction.place_bid') }}" aria-label="{{ trans('packages.auction.place_bid') }}">
                    <i class="fal fa-gavel"></i>
                  </a>
                @else
                  <a class="btn btn-primary sc-add-to-cart add-to-card-mod" data-link="{{ route('cart.addItem', $item->slug) }}" data-toggle="tooltip" title="@lang('theme.add_to_cart')" aria-label="@lang('theme.add_to_cart')">
                    <i class="far fa-shopping-cart"></i>
                  </a>
                @endif
              </div> <!-- /.product-actions -->

              <div class="product-info">
                @if (is_incevio_package_loaded('auction') && $item->auctionable)
                  @include('auction::frontend._auction_status')
                @else
                  @include('theme::layouts.ratings', ['ratings' => $item->ratings, 'count' => $item->ratings_count])
                @endif

                <a href="{{ route('show.product', $item->slug) }}" class="product-info-title" data-name="product_name" aria-label="{{ $item->title }}">{{ $item->title }}</a>

                <div class="product-info-availability">
                  @lang('theme.availability'): <span>{{ $item->stock_quantity > 0 ? trans('theme.in_stock') : trans('theme.out_of_stock') }}</span>
                </div>

                @include('theme::layouts.pricing', ['item' => $item])

                <div class="product-info-desc"> {!! $item->description !!} </div>
                {{-- data-limit-count="150" --}}
                <ul class="product-info-feature-list">
                  @if (config('system_settings.show_item_conditions'))
                    <li>{!! $item->condition !!}</li>
                  @endif
                  {{-- <li>{{ $item->manufacturer->name }}</li> --}}
                </ul>
              </div><!-- /.product-info -->
            </div><!-- /.product -->
          </div> <!-- /.col-md-* -->
        @empty
          <div class="col-12 lead text-center my-5">
            <p class="mb-3">{{ trans('theme.no_product_found') }}</p>

            <a href="{{ url('categories') }}" class="btn btn-primary btn-sm">
              {{ trans('theme.button.choose_from_categories') }}
            </a>
          </div> <!-- /.col-12 -->
        @endforelse
      </div><!-- /.row -->


      <div class="row pagenav-wrapper my-4">
        <div class="col-12">
          {{ $products->appends(request()->input())->links('theme::layouts.pagination') }}
        </div>
      </div><!-- /.row .pagenav-wrapper -->
    </div><!-- /.col-sm-9 -->
  </div><!-- /.row .product-list-wrapper -->

  <hr class="dotted" />
@endisset
