@if (Auth::user()->isFromPlatform())
  <td>{{ $order->shop->getName() }}</td>
@endif
