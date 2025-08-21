<div class="dashboard-section">
  <div class="row">
    <div class="col-12 no-gutters">
      <div class="my-info-container">
        @if (\App\Models\SystemConfig::CustomerNeedsApproval() && !Auth::guard('customer')->user()->isApproved())
          <div class="notice notice-info notice-md" id="customer-not-approved-notice">
            <strong>{{ trans('app.account_pending_for_approval') }}</strong> {{ trans('help.account_pending_for_approval') }}
          </div>
        @endif

        <div class="my-info-box radius-top p-3 border">
          <div class="me-info-block">
            <div class="my-photo-block">
              <img src="{{ get_storage_file_url(optional($dashboard->image)->path, 'thumbnail') }}" class="center-block" alt="{{ trans('theme.avatar') }}" />
            </div>

            <div class="my-info">
              <div class="name">
                <span>
                  {{ $dashboard->getName() }}
                </span>
                {{-- For approved customers show customer approved icon --}}
                @if (\App\Models\SystemConfig::CustomerNeedsApproval() && Auth::guard('customer')->user()->isApproved())
                  <em class="fas fa-user-check no-fill" style="color:green" data-toggle="tooltip" data-title="{{ trans('app.statuses.approved') }}"></em>
                @endif

                <a href="{{ route('account', 'account') }}" class="small pl-2">
                  <i class="fas fa-edit" data-toggle="tooltip" data-title="{{ trans('theme.edit_account') }}"></i>
                </a>
              </div>

              <div class="text-muted">
                <small>
                  <i class="fas fa-clock-o no-fill"></i>
                  {{ trans('theme.member_since') }}: <em>{{ $dashboard->created_at->diffForHumans() }}</em>
                </small>

                @if (is_incevio_package_loaded('buyerGroup'))
                  <br>
                  <small>
                    @if (isset($dashboard->buyerGroup))
                      <span class="text-info">
                        <i class="fa fa-users text-info" data-toggle="tooltip" title="{{ trans('packages.buyer_group') }}"></i>
                        {{ $dashboard->buyerGroup->name }} <a href="{{ url('/my/account#buyer-group-tab') }}"><em class="fas fa-edit" data-toggle="tooltip" data-title="{{ trans('packages.change_buyer_group') }}"> </em></a>
                      </span>
                    @endif
                  </small>
                @endif
              </div>
            </div>

            <div class="pull-right">
              @unless ($dashboard->shippingAddress)
                <a href="{{ route('account', 'account') }}#address-tab" class="btn btn-default">
                  <i class="fas fa-truck"></i> @lang('theme.add_shipping_address')
                </a>
              @endunless

              <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart no-fill"></i> @lang('theme.button.continue_shopping')
              </a>
            </div>
          </div>
        </div><!-- .my-info-box -->

        <div class="my-info-details border radius-bottom">
          <ul>
            <li>
              <a href="{{ route('account', 'orders') }}">
                <span class="v">{{ $dashboard->orders_count }}</span>
                <span class="d">
                  <i class="fas fa-shopping-cart no-fill"></i> @lang('theme.orders')
                </span>
              </a>
            </li>
            <li class="devider">|</li>
            <li>
              <a href="{{ route('account', 'wishlist') }}">
                <span class="v">{{ $dashboard->wishlists_count }}</span>
                <span class="d">
                  <i class="fas fa-heart no-fill"></i> @lang('theme.wishlist')
                </span>
              </a>
            </li>
            <li class="devider">|</li>
            <li>
              <a href="{{ route('account', 'messages') }}">
                <span class="v">{{ $dashboard->messages_count }}</span>
                <span class="d">
                  <i class="fas fa-envelope no-fill"></i> @lang('theme.unread_messages')
                </span>
              </a>
            </li>
            <li class="devider">|</li>
            <li>
              <a href="{{ route('account', 'coupons') }}">
                <span class="v">{{ $dashboard->coupons_count }}</span>
                <span class="d">
                  <i class="fas fa-tags no-fill"></i> @lang('theme.coupons')
                </span>
              </a>
            </li>
            <li>|</li>
            <li>
              <a href="{{ route('account', 'disputes') }}">
                <span class="v">{{ $dashboard->disputes_count }}</span>
                <span class="d">
                  <i class="fas fa-envelope no-fill"></i> @lang('theme.disputes')
                </span>
              </a>
            </li>
          </ul>
        </div><!-- .my-info-details -->
      </div><!-- .my-info-container -->
    </div><!-- .col-sm-12 -->
  </div><!-- .row -->

  <div class="row mb-5">
    <div class="col-md-6 pr-1">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>{{ trans('theme.date') }}</th>
              <th>
                {{ trans('theme.orders') }}
                <i class="fas fa-question-circle pull-right no-fill" data-toggle="tooltip" data-title="{{ trans('theme.item_count') }}"></i>
              </th>
              <th>{{ trans('theme.amount') }}</th>
            </tr>
          </thead>

          <tbody>
            @foreach ($dashboard->orders as $order)
              <tr>
                <td>{!! $order->created_at->format('M j') !!}</td>
                <td>
                  <img src="{{ get_storage_file_url(optional($order->shop->logoImage)->path, 'mini') }}" class="mr-2" alt="{{ $order->shop->name }}" data-toggle="tooltip" data-title="{{ $order->shop->name }}">

                  <a href="{{ route('order.detail', $order) }}">
                    {!! $order->order_number !!}
                  </a>

                  <small class="pl-2">{!! $order->orderStatus() !!}</small>
                  <span class="label label-outline pull-right"> {{ $order->item_count }} </span>
                </td>

                <td>{!! get_formated_currency($order->grand_total, 2, $order->currency_id) !!}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div> <!-- /.table-responsive -->
    </div> <!-- .col-sm-6 -->

    <div class="col-md-6 pl-1">
      <div class="table-responsive">
        <table class="table table-bordered radius">
          <thead>
            <tr>
              <th>{{ trans('theme.wishlist') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($dashboard->wishlists as $wish)
              @if ($wish->inventory)
                <tr>
                  <td>
                    <img src="{{ get_product_img_src($wish->inventory, 'tiny_thumb') }}" alt="{{ $wish->inventory->title }}" title="{{ $wish->inventory->title }}" />

                    <a class="product-link" href="{{ route('show.product', $wish->inventory->slug) }}">{!! \Illuminate\Support\Str::limit($wish->inventory->title, 35) !!}</a>
                  </td>
                  <td>
                    <a class="btn btn-primary" href="{{ route('direct.checkout', $wish->inventory->slug) }}">
                      <i class="fas fa-rocket no-fill"></i> @lang('theme.button.buy_now')
                    </a>
                  </td>
                </tr>
              @elseif($wish->product)
                <tr>
                  <td>
                    <img src="{{ get_storage_file_url(optional($wish->product->featureImage)->path, 'tiny') }}" alt="{{ $wish->product->name }}" title="{{ $wish->product->name }}" />

                    <a class="product-link" href="{{ route('show.offers', $wish->product->slug) }}" class="btn btn-sm btn-link">{{ \Illuminate\Support\Str::limit($wish->product->name, 35) }}</a>
                  </td>

                  <td>
                    <a class="btn btn-primary btn-xs" href="{{ route('show.offers', $wish->product->slug) }}">
                      @lang('theme.view_more_offers', ['count' => $wish->product->inventories_count])
                    </a>
                  </td>
                </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      </div> <!-- /.table-responsive -->
    </div> <!-- .col-sm-6 -->
  </div><!-- .row -->

  @if(is_incevio_package_loaded('auction') && $dashboard->bids_count > 0)
    @include('auction::frontend._dashboard_bid_table')
  @endif

  @if (is_incevio_package_loaded('buyerGroup'))
    @include('buyerGroup::charts.customerCharts')

    @include('buyerGroup::partials._customer_report_section')
  @endif
</div>
