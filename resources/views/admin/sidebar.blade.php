{{--   Left side column. contains the logo and sidebar --}}
<aside class="main-sidebar">
  {{-- sidebar: style can be found in sidebar.less --}}
  <section class="sidebar">
    <ul class="sidebar-menu">
      <li class="{{ Request::is('admin/dashboard*') ? 'active' : '' }}">
        <a href="{{ url('admin/dashboard') }}">
          <i class="fa fa-dashboard"></i> <span>{{ trans('nav.dashboard') }}</span>
        </a>
      </li>

      @if (Gate::allows('index', \App\Models\Category::class) || Gate::allows('index', \App\Models\Attribute::class) || Gate::allows('index', \App\Models\Product::class) || Gate::allows('index', \App\Models\Manufacturer::class) || Gate::allows('index', \App\Models\CategoryGroup::class) || Gate::allows('index', \App\Models\CategorySubGroup::class))
        <li class="treeview {{ Request::is('admin/catalog*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-tags"></i>
            <span>{{ trans('nav.catalog') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @if (Gate::allows('index', \App\Models\Category::class) || Gate::allows('index', \App\Models\CategoryGroup::class) || Gate::allows('index', \App\Models\CategorySubGroup::class))
              <li class="{{ Request::is('admin/catalog/category*') ? 'active' : '' }}">
                <a href="javascript:void(0)">
                  <i class="fa fa-angle-double-right"></i>
                  {{ trans('nav.categories') }}
                  <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  @can('index', \App\Models\CategoryGroup::class)
                    <li class="{{ Request::is('admin/catalog/categoryGroup*') ? 'active' : '' }}">
                      <a href="{{ route('admin.catalog.categoryGroup.index') }}">
                        <i class="fa fa-angle-right"></i>{{ trans('nav.groups') }}
                      </a>
                    </li>
                  @endcan

                  @can('index', \App\Models\CategorySubGroup::class)
                    <li class="{{ Request::is('admin/catalog/categorySubGroup*') ? 'active' : '' }}">
                      <a href="{{ route('admin.catalog.categorySubGroup.index') }}">
                        <i class="fa fa-angle-right"></i>{{ trans('nav.sub-groups') }}
                      </a>
                    </li>
                  @endcan

                  @can('index', \App\Models\Category::class)
                    <li class="{{ Request::is('admin/catalog/category') ? 'active' : '' }}">
                      <a href="{{ url('admin/catalog/category') }}">
                        <i class="fa fa-angle-right"></i>{{ trans('nav.categories') }}
                      </a>
                    </li>
                  @endcan
                </ul>
              </li>
            @endif

            @can('index', \App\Models\Attribute::class)
              <li class="{{ Request::is('admin/catalog/attribute*') ? 'active' : '' }}">
                <a href="{{ url('admin/catalog/attribute') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.attributes') }}
                </a>
              </li>
            @endcan

            @if (is_catalog_enabled())
              @can('index', \App\Models\Product::class)
                <li class="{{ Request::is('admin/catalog/product*') ? 'active' : '' }}">
                  <a href="{{ url('admin/catalog/product') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.products') }}
                  </a>
                </li>
              @endcan
            @endif

            @can('index', \App\Models\Manufacturer::class)
              <li class="{{ Request::is('admin/catalog/manufacturer*') ? 'active' : '' }}">
                <a href="{{ url('admin/catalog/manufacturer') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.manufacturers') }}
                </a>
              </li>
            @endcan
          </ul>
        </li>
      @endif

      @if (Gate::allows('index', \App\Models\Inventory::class) || Gate::allows('index', \App\Models\Warehouse::class) || Gate::allows('index', \App\Models\Supplier::class))
        <li class="treeview {{ Request::is('admin/stock*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-cubes"></i>
            <span>{{ trans('nav.stock') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @if (is_catalog_enabled())
              @can('index', \App\Models\Inventory::class)
                <li class="{{ (Request::is('admin/stock/inventory/physical') && !(Request::is('admin/stock/inventory/digital*') || Request::is('admin/stock/inventory/auction*'))) || (isset($inventory) && isset($product) && !$product->downloadable && !$inventory->auctionable) ? 'active' : '' }}">
                  <a href="{{ route('admin.stock.inventory.index', ['type' => 'physical']) }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.physical_products') }}
                  </a>
                </li>

                <li class="{{ Request::is('admin/stock/inventory/digital') || (isset($product) && $product->downloadable) ? 'active' : '' }}">
                  <a href="{{ route('admin.stock.inventory.index', ['type' => 'digital']) }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.digital_products') }}
                  </a>
                </li>

                @if (is_incevio_package_loaded('auction'))
                  @include('auction::admin._sidebar_nav_inventory')
                @endif
              @endcan
            @endif

            @if (!is_catalog_enabled() && Auth::user()->isFromMerchant())
              @can('index', \App\Models\Product::class)
                <li class="{{ Request::is('admin/stock/product/physical*') ? 'active' : '' }}">
                  <a href="{{ url('admin/stock/product/physical') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.physical_products') }}
                  </a>
                </li>

                <li class="{{ Request::is('admin/stock/product/digital*') ? 'active' : '' }}">
                  <a href="{{ url('admin/stock/product/digital') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.digital_products') }}
                  </a>
                </li>

                @if (is_incevio_package_loaded('auction'))
                  @include('auction::admin._sidebar_nav_product')
                @endif
              @endcan
            @endif

            @can('index', \App\Models\Warehouse::class)
              <li class="{{ Request::is('admin/stock/warehouse*') ? 'active' : '' }}">
                <a href="{{ url('admin/stock/warehouse') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.warehouses') }}
                </a>
              </li>
            @endcan

            @can('index', \App\Models\Supplier::class)
              <li class="{{ Request::is('admin/stock/supplier*') ? 'active' : '' }}">
                <a href="{{ url('admin/stock/supplier') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.suppliers') }}
                </a>
              </li>
            @endcan

            @if (is_incevio_package_loaded('shopify'))
              @include('shopify::_sidebar_admin_nav')
            @endif
          </ul>
        </li>
      @endif

      {{-- POS system menu --}}
      @if (is_incevio_package_loaded('pos'))
        @include('pos::_sidebar_option')
      @endif

      @if (Gate::allows('index', \App\Models\Order::class) || Gate::allows('index', \App\Models\Cart::class))
        <li class="treeview {{ Request::is('admin/order*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-cart-plus"></i>
            <span>{{ trans('nav.orders') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @can('index', \App\Models\Order::class)
              <li class="{{ Request::is('admin/order/order*') ? 'active' : '' }}">
                <a href="{{ url('admin/order/order') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.orders') }}
                </a>
              </li>
            @endcan

            @can('index', \App\Models\Cart::class)
              <li class="{{ Request::is('admin/order/cart*') ? 'active' : '' }}">
                <a href="{{ url('admin/order/cart') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.carts') }}
                </a>
              </li>
            @endcan

            @can('cancelAny', \App\Models\Order::class)
              <li class="{{ Request::is('admin/order/cancellation*') ? 'active' : '' }}">
                <a href="{{ url('admin/order/cancellation') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.cancellations') }}
                </a>
              </li>
            @endcan
          </ul>
        </li>
      @endif

      @if (Gate::allows('index', \App\Models\User::class) || Gate::allows('index', \App\Models\Customer::class) || Gate::allows('index', \Incevio\Package\Inspector\Models\InspectorModel::class))
        <li class="treeview {{ Request::is('admin/admin*') || Request::is('address/addresses/customer*') || Request::is('admin/inspector*') || Request::is('admin/affiliate') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-user-secret"></i>
            <span>{{ trans('nav.admin') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @can('index', \App\Models\User::class)
              <li class="{{ Request::is('admin/admin/user*') ? 'active' : '' }}">
                <a href="{{ url('admin/admin/user') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.users') }}
                </a>
              </li>
            @endcan

            @if (Auth::user()->isMerchant())
              <li class="{{ Request::is('admin/admin/deliveryboys*') ? 'active' : '' }}">
                <a href="{{ route('admin.admin.deliveryboy.index') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.delivery_boys') }}
                </a>
              </li>
            @endif

            @can('index', \App\Models\Customer::class)
              <li class="{{ Request::is('admin/admin/customer*') || Request::is('address/addresses/customer*') ? 'active' : '' }}">
                <a href="{{ url('admin/admin/customer') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.customers') }}
                </a>
              </li>
            @endcan

            @if (is_incevio_package_loaded('affiliate') && Auth::user()->isSuperAdmin())
              @include('affiliate::admin._sidebar_nav')
            @endif

            @if ((Auth::user()->isAdmin() || Gate::allows('index', \Incevio\Package\Inspector\Models\InspectorModel::class)) && is_incevio_package_loaded('inspector'))
              @can('index', \Incevio\Package\Inspector\Models\InspectorModel::class)
                <li class="{{ Request::is('admin/inspector/inspectables*') ? 'active' : '' }}">
                  <a href="{{ url('admin/inspector/inspectables') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('packages.inspector.inspectables') }}
                    @include('partials._addon_badge')
                  </a>
                </li>
              @endcan
            @endif
          </ul>
        </li>
      @endif

      @if (Gate::allows('index', \App\Models\Merchant::class) || Gate::allows('index', \App\Models\Shop::class))
        <li class="treeview {{ Request::is('admin/seller*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-bar-chart"></i>
            <span>{{ trans('nav.vendors') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @can('index', \App\Models\Shop::class)
              <li class="{{ Request::is('admin/seller/merchant*') ? 'active' : '' }}">
                <a href="{{ url('admin/seller/merchant') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.merchants') }}
                </a>
              </li>
            @endcan

            @can('index', \App\Models\Shop::class)
              <li class="{{ Request::is('admin/seller/shop*') ? 'active' : '' }}">
                <a href="{{ url('admin/seller/shop') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.shops') }}
                </a>
              </li>
            @endcan
          </ul>
        </li>
      @endif

      @if (is_incevio_package_loaded('buyerGroup') && Auth::user()->isAdmin())
        @include('buyerGroup::_sidebar_admin_nav')
      @endif

      @if (is_incevio_package_loaded('wallet'))
        @include('wallet::admin.sidebar._main_dropdowns')
      @endif

      @if (is_incevio_package_loaded('smsGateways'))
        @include('smsGateways::partials._sidebar')
      @endif

      @if (Auth::user()->isFromMerchant())
        @if (Gate::allows('index', \App\Models\Carrier::class) || Gate::allows('index', \Incevio\Package\Packaging\Models\Packaging::class))
          <li class="treeview {{ Request::is('admin/shipping*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
              <i class="fa fa-truck"></i>
              <span>{{ trans('nav.shipping') }}</span>
              <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              @can('index', \App\Models\Carrier::class)
                <li class="{{ Request::is('admin/shipping/carrier*') ? 'active' : '' }}">
                  <a href="{{ url('admin/shipping/carrier') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.carriers') }}
                  </a>
                </li>
              @endcan

              @if (is_incevio_package_loaded('shippo'))
                <li class=" {{ Request::is('admin/shipping/shippo*') ? 'active' : '' }}">
                  <a href="{{ route('admin.shipping.shippo.index') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('packages.shippo.shippo') }}
                  </a>
                </li>
              @endif

              @if (is_incevio_package_loaded('packaging'))
                @can('index', \Incevio\Package\Packaging\Models\Packaging::class)
                  <li class="{{ Request::is('admin/shipping/packaging*') ? 'active' : '' }}">
                    <a href="{{ url('admin/shipping/packaging') }}">
                      <i class="fa fa-angle-double-right"></i> {{ trans('nav.packaging') }}
                      @include('partials._addon_badge')
                    </a>
                  </li>
                @endcan
              @endif

              @can('index', \App\Models\ShippingZone::class)
                <li class="{{ Request::is('admin/shipping/shippingZone*') ? 'active' : '' }}">
                  <a href="{{ url('admin/shipping/shippingZone') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.shipping_zones') }}
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endif
      @endif
      {{-- temporarily hidden from super admin --}}
      @if (Auth::user()->isFromMerchant())
        <li class="treeview {{ Request::is('admin/promotion*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-paper-plane"></i>
            <span>{{ trans('nav.promotions') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>

          <ul class="treeview-menu">
            @can('index', \App\Models\Coupon::class)
              <li class="{{ Request::is('admin/promotion/coupon*') ? 'active' : '' }}">
                <a href="{{ url('admin/promotion/coupon') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.coupons') }}
                </a>
              </li>
            @endcan

            <li class="{{ Request::is('admin/promotions*') ? 'active' : '' }}">
              <a href="{{ url('admin/promotions') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.promotions') }}
              </a>
            </li>

            {{-- @can('index', \App\Models\GiftCard::class)
                  <li class="{{ Request::is('admin/promotion/giftCard*') ? 'active' : '' }}">
                    <a href="{{ url('admin/promotion/giftCard') }}">
                      <i class="fa fa-angle-double-right"></i> {{ trans('nav.gift_cards') }}
                    </a>
                  </li>
                @endcan --}}
          </ul>
        </li>
      @endif

      @if (Gate::allows('index', \App\Models\Message::class) || Gate::allows('index', \App\Models\Ticket::class) || Gate::allows('index', \App\Models\Dispute::class) || Gate::allows('index', \App\Models\Refund::class) || Gate::allows('index', \Incevio\Package\LiveChat\Models\ChatConversation::class))
        <li class="treeview {{ Request::is('admin/support*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-support"></i>
            <span>{{ trans('nav.support') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @if (is_incevio_package_loaded('liveChat'))
              @can('index', \Incevio\Package\LiveChat\Models\ChatConversation::class)
                <li class="{{ Request::is('admin/support/chat*') ? 'active' : '' }}">
                  <a href="{{ url('admin/support/chat') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.chats') }}
                    @include('partials._addon_badge')
                  </a>
                </li>
              @endcan
            @endif

            @can('index', \App\Models\Message::class)
              <li class="{{ Request::is('admin/support/message*') ? 'active' : '' }}">
                <a href="{{ url('admin/support/message/labelOf/' . \App\Models\Message::LABEL_INBOX) }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.support_messages') }}
                </a>
              </li>
            @endcan

            @if (Auth::user()->isFromPlatform())
              @can('index', \App\Models\Ticket::class)
                <li class="{{ Request::is('admin/support/ticket*') ? 'active' : '' }}">
                  <a href="{{ url('admin/support/ticket') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('nav.support_tickets') }}
                  </a>
                </li>
              @endcan
            @endif

            @can('index', \App\Models\Dispute::class)
              <li class="{{ Request::is('admin/support/dispute*') ? 'active' : '' }}">
                <a href="{{ url('admin/support/dispute') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.disputes') }}
                </a>
              </li>
            @endcan

            @can('index', \App\Models\Refund::class)
              <li class="{{ Request::is('admin/support/refund*') ? 'active' : '' }}">
                <a href="{{ url('admin/support/refund') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.refunds') }}
                </a>
              </li>
            @endcan
          </ul>
        </li>
      @endif

      @if ((new \App\Helpers\Authorize(Auth::user(), 'customize_appearance'))->check())
        <li class="treeview {{ Request::is('admin/appearance*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-paint-brush"></i>
            <span>{{ trans('nav.appearance') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @unless (Auth::user()->isMerchant())
              <li class="{{ Request::is('admin/appearance/theme') ? 'active' : '' }}">
                <a href="{{ url('admin/appearance/theme') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.themes') }}
                </a>
              </li>

              @if (is_incevio_package_loaded('dynamic-popup'))
                <li class="{{ Request::is('admin/appearance/popup*') ? 'active' : '' }}">
                  <a href="{{ route('admin.appearance.popup') }}">
                    <i class="fa fa-angle-double-right"></i> {{ trans('DynamicPopup::lang.dynamic_popups') }}
                  </a>
                </li>
              @endif
            @endunless

            <li class="{{ Request::is('admin/appearance/banner*') ? 'active' : '' }}">
              <a href="{{ url('admin/appearance/banner') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.banners') }}
              </a>
            </li>

            <li class="{{ Request::is('admin/appearance/slider*') ? 'active' : '' }}">
              <a href="{{ url('admin/appearance/slider') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.sliders') }}
              </a>
            </li>

            <li class="{{ Request::is('admin/appearance/custom_css*') ? 'active' : '' }}">
              <a href="{{ route('admin.appearance.custom_css') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.custom_css') }}
              </a>
            </li>
          </ul>
        </li>
      @endif

      {{-- Flash deal merge into promotions --}}
      @if (Auth::user()->isAdmin() || (new \App\Helpers\Authorize(Auth::user(), 'manage_flash_deal'))->check())
        <li class="treeview {{ Request::is('admin/promotions*') || Request::is('admin/flashdeal*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-bullhorn"></i>
            <span>{{ trans('nav.promotions') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @if (Auth::user()->isAdmin())
              <li class="{{ Request::is('admin/promotions*') ? 'active' : '' }}">
                <a href="{{ url('admin/promotions') }}">
                  <i class="fa fa-angle-double-right"></i> <span>{{ trans('nav.promotions') }}</span>
                </a>
              </li>
            @endif

            @if (Auth::user()->isAdmin() && is_incevio_package_loaded('trendingKeywords'))
              <li class="{{ Request::is('admin/promotions/trendingKeywords*') ? 'active' : '' }}">
                <a href="{{ route('admin.promotion.trendingKeywords') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('packages.trendingKeywords.trending_keywords') }}
                  @include('partials._addon_badge')
                </a>
              </li>
            @endif

            @if ((new \App\Helpers\Authorize(Auth::user(), 'manage_flash_deal'))->check())
              <li class="{{ Request::is('admin/flashdeal*') ? 'active' : '' }}">
                <a href="{{ route('admin.flashdeal') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('theme.flash_deal') }}
                </a>
              </li>
            @endif
          </ul>
        </li>
      @endif

      @if (Auth::user()->isAdmin())
        <li class="{{ Request::is('admin/packages*') ? 'active' : '' }}">
          <a href="{{ url('admin/packages') }}">
            <i class="fa fa-plug"></i> <span>{{ trans('nav.packages') }}</span>
          </a>
        </li>
      @endif

      <li class="treeview {{ Request::is('admin/setting*') ? 'active' : '' }}">
        <a href="javascript:void(0)">
          <i class="fa fa-gears"></i>
          <span>{{ trans('nav.settings') }}</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>

        <ul class="treeview-menu">
          @if (is_subscription_enabled())
            @can('index', \App\Models\SubscriptionPlan::class)
              <li class="{{ Request::is('admin/setting/subscriptionPlan*') ? 'active' : '' }}">
                <a href="{{ url('admin/setting/subscriptionPlan') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.subscription_plans') }}
                </a>
              </li>
            @endcan
          @endif

          @can('index', \App\Models\Role::class)
            <li class="{{ Request::is('admin/setting/role*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/role') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.user_roles') }}
              </a>
            </li>
          @endcan

          @can('index', \App\Models\Tax::class)
            <li class="{{ Request::is('admin/setting/tax*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/tax') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.taxes') }}
              </a>
            </li>
          @endcan

          @can('view', \App\Models\Config::class)
            <li class="{{ Request::is('admin/setting/general*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/general') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.shop_settings') }}
              </a>
            </li>

            <li class="{{ Request::is('admin/setting/config*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/config') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.configurations') }}
              </a>
            </li>

            @if (vendor_get_paid_directly() || vendor_can_on_off_payment_method())
              <li class=" {{ Request::is('admin/setting/paymentMethod*') ? 'active' : '' }}">
                <a href="{{ url('admin/setting/paymentMethod') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.payment_methods') }}
                </a>
              </li>
            @endif

            <li class=" {{ Request::is('admin/setting/verify*') ? 'active' : '' }}">
              <a href="{{ route('admin.setting.verify') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('app.get_verified') }}
              </a>
            </li>
          @endcan

          @can('view', \App\Models\System::class)
            <li class="{{ Request::is('admin/setting/system/general*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/system/general') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.system_settings') }}
              </a>
            </li>
          @endcan

          @can('view', \App\Models\SystemConfig::class)
            <li class="{{ Request::is('admin/setting/system/config*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/system/config') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.configurations') }}
              </a>
            </li>
          @endcan

          @if (is_incevio_package_loaded('announcement') && Auth::user()->isAdmin())
            <li class="{{ Request::is('admin/setting/announcement*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/announcement') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.announcements') }}
                @include('partials._addon_badge')
              </a>
            </li>
          @endif

          @if (Auth::user()->isAdmin())
            <li class="{{ Request::is('admin/setting/country*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/country') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.countries') }}
              </a>
            </li>

            <li class="{{ Request::is('admin/setting/currency*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/currency') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.currencies') }}
              </a>
            </li>

            <li class="{{ Request::is('admin/setting/language*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/language') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('app.languages') }}
              </a>
            </li>
          @endif

          @if (is_incevio_package_loaded('wallet'))
            @include('wallet::admin.sidebar._setting_option')
          @endif

          @if (is_incevio_package_loaded('inspector') && Gate::allows('setting', \Incevio\Package\Inspector\Models\InspectorModel::class))
            <li class="{{ Request::is('admin/setting/inspector*') ? 'active' : '' }}">
              <a href="{{ route(config('inspector.routes.settings')) }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('packages.inspector.inspector_settings') }}
                @include('partials._addon_badge')
              </a>
            </li>
          @endif

          @if (is_incevio_package_loaded('aiAssistant'))
            @include('aiAssistant::sidebar_config_option')
          @endif

          @if (is_incevio_package_loaded('zipcode'))
            @include('zipcode::_nav_admin_sidebar')
          @endif

          @if (is_incevio_package_loaded('dynamicCommission') && (new \App\Helpers\Authorize(Auth::user(), 'manage_dynamic_commission'))->check())
            <li class="{{ Request::is('admin/setting/dynamicCommission*') ? 'active' : '' }}">
              <a href="{{ route(config('dynamicCommission.routes.settings')) }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('packages.dynamicCommission.commissions_settings') }}
                @include('partials._addon_badge')
              </a>
            </li>
          @endif

          @if (is_incevio_package_loaded('searchAutocomplete') && Auth::user()->isAdmin())
            <li class="{{ Request::is('admin/setting/autocomplete*') ? 'active' : '' }}">
              <a href="{{ route('admin.setting.autocomplete') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('packages.searchAutocomplete.search_settings') }}
                @include('partials._addon_badge')
              </a>
            </li>
          @endif

          @if (is_incevio_package_loaded('ebay'))
            <li class="{{ Request::is('admin/setting/ebay*') ? 'active' : '' }}">
              <a href="{{ url('admin/setting/ebay') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('ebay::lang.ebay_settings') }}
              </a>
            </li>
          @endif
        </ul>
      </li>

      @if (Auth::user()->isAdmin() || Gate::allows('index', \App\Models\Page::class) || Gate::allows('index', \App\Models\EmailTemplate::class) || Gate::allows('index', \App\Models\Blog::class) || Gate::allows('index', \App\Models\Faq::class))
        <li class="treeview {{ Request::is('admin/utility*') ? 'active' : '' }}">
          <a href="javascript:void(0)">
            <i class="fa fa-asterisk"></i>
            <span>{{ trans('nav.utilities') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @can('index', \App\Models\EmailTemplate::class)
              <li class="{{ Request::is('admin/utility/emailTemplate*') ? 'active' : '' }}">
                <a href="{{ url('admin/utility/emailTemplate') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.email_templates') }}
                </a>
              </li>
            @endcan

            @if (Auth::user()->isAdmin())
              <li class="{{ Request::is('admin/utility/pdfTemplate*') ? 'active' : '' }}">
                <a href="{{ route('admin.utility.pdfTemplate.index') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.pdf_template') }}
                </a>
              </li>
            @endcan

            @if (Auth::user()->isAdmin() && is_incevio_package_loaded('smartForm'))
              @include('smartForm::_nav_admin_sidebar')
            @endif

            @can('index', \App\Models\Page::class)
              <li class="{{ Request::is('admin/utility/page*') ? 'active' : '' }}">
                <a href="{{ url('admin/utility/page') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.pages') }}
                </a>
              </li>
            @endcan

            @can('index', \App\Models\Blog::class)
              <li class="{{ Request::is('admin/utility/blog*') ? 'active' : '' }}">
                <a href="{{ url('admin/utility/blog') }}">
                  <i class="fa fa-angle-double-right"></i> <span>{{ trans('nav.blogs') }}</span>
                </a>
              </li>
            @endcan

            @if (is_incevio_package_loaded('eventy'))
              @can('index', \Incevio\Package\Eventy\Models\Event::class)
                <li class="{{ Request::is('admin/utility/event*') ? 'active' : '' }}">
                  <a href="{{ url('admin/utility/event') }}">
                    <i class="fa fa-angle-double-right"></i> <span>{{ trans('packages.eventy.events') }}</span>
                    @include('partials._addon_badge')
                  </a>
                </li>
              @endcan
            @endif

            @can('index', \App\Models\Faq::class)
              <li class="{{ Request::is('admin/utility/faq*') ? 'active' : '' }}">
                <a href="{{ url('admin/utility/faq') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.faqs') }}
                </a>
              </li>
            @endcan
        </ul>
      </li>
    @endif

    @if (Auth::user()->isAdmin() || Auth::user()->isMerchant() || Gate::allows('report', \Incevio\Package\Wallet\Models\Wallet::class))
      <li class="treeview {{ Request::is('admin/report*') || Request::is('admin/shop/report*') ? 'active' : '' }}">
        <a href="javascript:void(0)">
          <i class="fa fa-bar-chart"></i>
          <span>{{ trans('nav.reports') }}</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>

        <ul class="treeview-menu">
          @if (is_incevio_package_loaded('wallet'))
            @include('wallet::admin.sidebar._report_option')
          @endif

          @if (Auth::user()->isAdmin())
            <li class="{{ Request::is('admin/report/kpi*') ? 'active' : '' }}">
              <a href="{{ route('admin.kpi') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.performance') }}
              </a>
            </li>

            <li class="{{ Request::is('admin/report/sales*') ? 'active' : '' }}">
              <a href="javascript:void(0)">
                <i class="fa fa-angle-double-right"></i>
                {{ trans('nav.sales') }}
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="{{ Request::is('admin/report/sales/orders*') ? 'active' : '' }}">
                  <a href="{{ route('admin.sales.orders') }}">
                    <i class="fa fa-angle-right"></i>{{ trans('nav.orders') }}
                  </a>
                </li>
                <li class="{{ Request::is('admin/report/sales/products*') ? 'active' : '' }}">
                  <a href="{{ route('admin.sales.products') }}">
                    <i class="fa fa-angle-right"></i>{{ trans('nav.products') }}
                  </a>
                </li>
                <li class="{{ Request::is('admin/report/sales/payment*') ? 'active' : '' }}">
                  <a href="{{ route('admin.sales.payments') }}">
                    <i class="fa fa-angle-right"></i>{{ trans('nav.payments') }}
                  </a>
                </li>
              </ul>
            </li>

            @if (is_incevio_package_loaded('googleAnalytics'))
              <li class="{{ Request::is('admin/report/googleAnalytics*') ? 'active' : '' }}">
                <a href="{{ route('admin.report.googleAnalytics') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('packages.analytics.analytics') }}
                  @include('partials._addon_badge')
                </a>
              </li>
            @endif

            @if (config('report.collect_visitor_data'))
              <li class="{{ Request::is('admin/report/visitors*') ? 'active' : '' }}">
                <a href="{{ route('admin.report.visitors') }}">
                  <i class="fa fa-angle-double-right"></i> {{ trans('nav.visitors') }}
                </a>
              </li>
            @endif
          @elseif(Auth::user()->isMerchant())
            <li class="{{ Request::is('admin/shop/report/kpi*') ? 'active' : '' }}">
              <a href="{{ route('admin.shop-kpi') }}">
                <i class="fa fa-angle-double-right"></i> {{ trans('nav.performance') }}
              </a>
            </li>
          @endif
        </ul>
      </li>
    @endif
  </ul>
</section> <!-- /.sidebar -->
</aside>
