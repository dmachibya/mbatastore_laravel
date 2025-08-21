@if ($orders->count() > 0)
  <div class="row mb-3">
    <div class="col-md-4">
      <form action="{{ url('/my/orders') }}" method="get" class="">
        <div class="form-inline d-flex">
          <input type="text" class="form-control" name="q" placeholder="Search by order id...">
          <button type="submit" class="btn btn-success d-inline"><i class="fa fa-search"></i></button>
        </div>
      </form>
    </div> <!-- /.col-md-4 -->
    <div class="col-md-4 mt-1">
      <h4 class="title">@lang('theme.your_order_history')</h4>
    </div> <!-- /.col-md-4 -->
    <div class="col-md-4">
    </div> <!-- /.col-md-4 -->
  </div>

  <div class="table-responsive">
    <table class="table" id="buyer-order-table">
      <tbody class="border-y">
        @foreach ($orders as $order)
          <tr class="order-info-head">
            <td width="40%">
              <h5 class="mb-2">
                <span>@lang('theme.order_id'): </span>
                <a href="{{ route('order.detail', $order) }}" data-toggle="tooltip" data-placement="left" title="{{ trans('theme.button.order_detail') }}">{{ $order->order_number }}</a>

                @if ($order->hasPendingCancellationRequest())
                  <span class="label label-warning pl-2 text-uppercase">
                    {{ trans('theme.' . $order->cancellation->request_type . '_requested') }}
                  </span>
                @elseif($order->hasClosedCancellationRequest())
                  <span class="pl-2">
                    {{ trans('theme.' . $order->cancellation->request_type) }}
                  </span>
                  {!! $order->cancellation->statusName() !!}
                @elseif($order->isCanceled())
                  <span class="ml-2">{!! $order->orderStatus() !!}</span>
                @endif

                @if ($order->dispute)
                  <span class="label label-danger ml-2 text-uppercase">@lang('theme.disputed')</span>
                @endif
              </h5>
              <h5>
                <span>@lang('theme.product_type'): </span>
                {{ $order->type }}
              </h5>
            </td>

            <td width="35%" class="store-info">
              <h5 class="mb-2">
                <span>@lang('theme.store'):</span>
                @if ($order->shop->slug)
                  <a href="{{ route('show.store', $order->shop->slug) }}"> {{ $order->shop->name }}</a>
                @else
                  @lang('theme.store_not_available')
                @endif

                <a href="{{ route('order.detail', $order) . '#message-section' }}" class="btn btn-xs btn-default ml-1">
                  @lang('theme.button.contact_seller')
                </a>
              </h5>

              <h5>
                <span>@lang('theme.status'): </span>
                {!! $order->orderStatus(true) . ' &nbsp; ' . $order->paymentStatusName() !!}
              </h5>
            </td>

            <td width="25%" class="order-amount">
              <h5 class="mb-2"><span>@lang('theme.order_amount'): </span>{{ get_formated_currency($order->grand_total, 2, $order->currency_id) }}</h5>
              <h5><span>@lang('theme.order_date'): </span>{{ $order->created_at->toDayDateTimeString() }}</h5>
            </td>
          </tr> <!-- /.order-info-head -->

          @foreach ($order->inventories as $item)
            <tr class="order-body">
              <td colspan="2">
                <div class="product-img-wrap">
                  <img class="lazy" src="{{ get_storage_file_url(optional($item->image)->path, 'tiny') }}" data-src="{{ get_storage_file_url(optional($item->image)->path, 'small') }}" alt="{{ $item->slug }}" title="{{ $item->slug }}" />
                </div>

                <div class="product-info">
                  {{ $item->pivot->item_description }}
                  <a href="{{ route('show.product', $item->slug) }}" class="ml-2" target="_blank" data-toggle="tooltip" data-placement="top" title="{{ trans('theme.show_product_page') }}">
                    <i class="fa fa-external-link" aria-hidden="true"></i>
                  </a>

                  @if (is_incevio_package_loaded('wallet') && is_wallet_credit_reward_enabled())
                    @if ($item->pivot->credit_back_amount)
                      @include('wallet::_credit_back_amount_badge', ['amount' => get_formated_currency($item->pivot->credit_back_amount)])
                    @endif
                  @endif

                  @if ($order->cancellation && $order->cancellation->isItemInRequest($item->id))
                    <span class="label label-danger pl-2">
                      {{ trans('theme.' . $order->cancellation->request_type . '_requested') }}
                    </span>
                  @endif

                  <div class="order-info-amount">
                    <span>{{ get_formated_currency($item->pivot->unit_price, 2, $order->currency_id) }} x {{ $item->pivot->quantity }}</span>
                  </div>

                  <ul class="mailbox-attachments clearfix pull-right">
                    @if (isset($item->attachments))
                      @foreach ($item->attachments as $attachment)
                        <li>
                          <div class="mailbox-attachment-info">
                            <span>
                              <a href="{{ route('order.attachment.download', ['attachment' => $attachment, 'order' => $order->id, 'inventory' => $item->id]) }}" class="btn btn-default btn-sm pull-right">@lang('theme.download') <i class="fa fa-cloud-download"></i></a>
                            </span>
                          </div>
                        </li>
                      @endforeach

                      @if (!is_null($item->download_limit) && !is_null($item->pivot->download) && $item->download_limit <= $item->pivot->download)
                        <span class="text-danger">@lang('theme.maximum_download_limit_reached')</span>
                      @elseif (!is_null($item->download_limit) && !is_null($item->pivot->download) && $item->download_limit > $item->pivot->download)
                        <span class="text-info">@lang('theme.download_left', ['download_number' => $item->download_limit - $item->pivot->download, 'download_limit' => $item->download_limit])</span>
                      @endif
                    @endif
                  </ul>
                </div>
              </td>

              @if ($loop->first)
                <td rowspan="{{ $loop->count }}" class="order-actions">
                  <a href="{{ route('order.again', $order) }}" class="btn btn-default btn-sm btn-block">
                    <i class="fas fa-shopping-cart"></i> @lang('theme.order_again')
                  </a>

                  @unless ($order->isCanceled())
                    <a href="{{ route('order.invoice', $order) }}" class="btn btn-default btn-sm btn-block">
                      <i class="fa fa-file-pdf-o" aria-hidden="true"></i> @lang('theme.invoice')
                    </a>

                    @if ($order->dispute)
                      <a href="{{ route('dispute.open', $order) }}" class="btn btn-default btn-sm btn-block" data-confirm="@lang('theme.confirm_action.open_a_dispute')">
                        <i class="fa fa-thumbs-o-down" aria-hidden="true"></i>
                        @lang('theme.dispute_detail')
                      </a>
                    @else
                      <a href="{{ route('dispute.open', $order) }}" class="confirm btn btn-default btn-sm btn-block" data-confirm="@lang('theme.confirm_action.open_a_dispute')">
                        <i class="fa fa-thumbs-o-down" aria-hidden="true"></i>
                        @lang('theme.button.open_dispute')
                      </a>
                    @endif

                    @if ($order->canBeCanceled())
                      {!! Form::model($order, ['method' => 'PUT', 'route' => ['order.cancel', $order]]) !!}
                      {!! Form::button('<i class="fas fa-times-circle-o"></i> ' . trans('theme.cancel_order'), ['type' => 'submit', 'class' => 'confirm btn btn-default btn-block flat', 'data-confirm' => trans('theme.confirm_action.cant_undo')]) !!}
                      {!! Form::close() !!}
                    @elseif($order->canRequestCancellation())
                      <a href="{{ route('cancellation.form', ['order' => $order, 'action' => 'cancel']) }}" class="modalAction btn btn-default btn-sm btn-block"><i class="fas fa-times"></i> @lang('theme.cancel_items')</a>
                    @endif

                    @if ($order->canTrack())
                      <a href="{{ route('order.track', $order) }}" class="btn btn-black btn-sm btn-block">
                        <i class="fas fa-map-marker"></i> @lang('theme.button.track_order')
                      </a>
                    @endif

                    @if ($order->canEvaluate())
                      <a href="{{ route('order.feedback', $order) }}" class="btn btn-primary btn-sm btn-block">
                        <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                        @lang('theme.button.give_feedback')
                      </a>
                    @endif

                    @if ($order->isFulfilled())
                      @if ($order->canRequestReturn())
                        <a href="{{ route('cancellation.form', ['order' => $order, 'action' => 'return']) }}" class="modalAction btn btn-default btn-sm btn-block"><i class="fas fa-undo"></i> @lang('theme.return_items')</a>
                      @endif

                      @unless ($order->goods_received)
                        {!! Form::model($order, ['method' => 'PUT', 'route' => ['goods.received', $order]]) !!}
                        {!! Form::button(trans('theme.button.confirm_goods_received'), ['type' => 'submit', 'class' => 'confirm btn btn-primary btn-block flat', 'data-confirm' => trans('theme.confirm_action.goods_received')]) !!}
                        {!! Form::close() !!}
                      @endunless
                    @endif
                  @endunless
                </td>
              @endif
            </tr> <!-- /.order-body -->
          @endforeach

          @if ($order->message_to_customer)
            <tr class="message_from_seller">
              <td colspan="3">
                <p>
                  <strong>@lang('theme.message_from_seller'): </strong> {{ $order->message_to_customer }}
                </p>
              </td>
            </tr>
          @endif

          @if ($order->buyer_note)
            <tr class="order-info-footer">
              <td colspan="3">
                <p class="order-detail-buyer-note">
                  <span>@lang('theme.note'): </span> {{ $order->buyer_note }}
                </p>
              </td>
            </tr>
          @endif
        @endforeach
      </tbody>
    </table>
  </div>
  <hr class="dotted" />
@else
  <p class="lead text-center border mb-5 p-5">
    @lang('theme.no_order_history')
    <br />
    <a href="{{ url('/') }}" class="btn btn-primary btn-sm">@lang('theme.button.shop_now')</a>
  </p>
@endif

<div class="row pagenav-wrapper mb-3">
  {{ $orders->links('theme::layouts.pagination') }}
</div><!-- /.row .pagenav-wrapper -->
