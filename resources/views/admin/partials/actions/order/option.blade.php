<td class="row-options">
  @can('fulfill', $order)
    @unless ($order->isFulfilled())
      @if ($order->deliver())
        <a href="javascript:void(0)" data-link="{{ route('admin.order.order.fulfillment', $order) }}" class="ajax-modal-btn">
          <i data-toggle="tooltip" data-placement="top" title="{{ trans('app.fulfill_order_delivery') }}" class="fa fa-truck"></i>
        </a>&nbsp;
      @elseif ($order->pickup())
        <a href="javascript:void(0)" data-link="{{ route('admin.order.order.edit', $order) }}" class="ajax-modal-btn">
          <i data-toggle="tooltip" data-placement="top" title="{{ trans('app.fulfill_order_pickup') }}" class="fa fa-shopping-basket"></i>
        </a>&nbsp;
      @endif
    @endunless
  @endcan

  <a href="{{ route('admin.order.order.show', $order->id) }}">
    <i data-toggle="tooltip" data-placement="top" title="{{ trans('app.open') }}" class="fa fa-expand"></i>
  </a>&nbsp;

  <a href="{{ route('admin.order.order.invoice', $order->id) }}">
    <i data-toggle="tooltip" data-placement="top" title="{{ trans('app.download_invoice') }}" class="fa fa-download"></i>
  </a>&nbsp;

  <a href="{{ route('order.shipping.label.download', $order) }}">
    <i data-toggle="tooltip" data-placement="top" title="{{ trans('app.download_shipping_label') }}" class="fa fa-file"></i>
  </a>&nbsp;

  @can('archive', $order)
    {!! Form::open([
        'route' => ['admin.order.order.archive', $order->id],
        'method' => 'delete',
        'class' => 'data-form',
    ]) !!}

    {!! Form::button('<i class="fa fa-archive text-muted"></i>', [
        'type' => 'submit',
        'class' => 'confirm ajax-silent',
        'title' => trans('app.order_archive'),
        'data-toggle' => 'tooltip',
        'data-placement' => 'top',
    ]) !!}

    {!! Form::close() !!}
  @endcan
</td>
