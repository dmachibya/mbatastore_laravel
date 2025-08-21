<td>
  @can('view', $order)
    <a href="{{ route('admin.order.order.show', $order->id) }}">
      {{ $order->order_number }}
    </a>
  @else
    {{ $order->order_number }}
  @endcan

  @if ($order->dispute)
    <span class="label label-danger">{{ trans('app.statuses.disputed') }}</span>
  @endif
</td>
