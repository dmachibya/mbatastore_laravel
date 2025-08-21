@if ($coupons->count() > 0)
  <h4 class="title mb-3">@lang('theme.coupons')</h4>

  <div class="table-responsive">
    <table class="table border" id="buyer-order-table">
      <thead>
        <tr>
          <th>{{ trans('theme.value') }}</th>
          <th>{{ trans('theme.store') }}</th>
          <th>{{ trans('theme.coupon_code') }}</th>
          <th width="30%">{{ trans('theme.validity') }}</th>
        </tr>
      </thead>

      <tbody>
        @foreach ($coupons as $coupon)
          <tr>
            <td class="text-center">
              @php
                $value = $coupon->type == 'amount' ? get_formated_currency($coupon->value, 2) : get_formated_decimal($coupon->value) . '%';
              @endphp

              <div class="customer-coupon-lists {{ $coupon->ending_time < \Carbon\Carbon::now() ? 'customer-coupons-expired' : '' }}">
                <div class="coupon-item">
                  <span class="customer-coupons-limit">
                    @if ($coupon->min_order_amount)
                      {{ trans('theme.when_min_order_value', ['value' => get_formated_currency($coupon->min_order_amount, 2)]) }}
                    @endif
                  </span>
                  <span class="customer-coupon-value">{{ trans('theme.coupon_off', ['value' => $value]) }}</span>
                </div>
              </div>
            </td>

            <td class="vertical-center">
              <a href="{{ route('show.store', $coupon->shop->slug) }}" target="_blank" rel="noopener">{{ $coupon->shop->name }}</a>
              <small><i class="far fa-external-link text-muted"></i></small>
            </td>
            <td class="text-center vertical-center">{{ $coupon->code }}</td>
            <td class="vertical-center"> {!! $coupon->validityText() !!}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <hr class="dotted" />
@else
  <p class="lead text-center border mb-5 p-5">
    @lang('theme.nothing_found')
  </p>
@endif

<div class="row pagenav-wrapper mb-3">
  {{ $coupons->links('theme::layouts.pagination') }}
</div><!-- /.row .pagenav-wrapper -->
