<script>
    var products = [];

      @foreach($cart->inventories as $item)
        var product = {
            'sku': '{{ $item->sku }}',
            'title': '{{ $item->title }}',
            'category': {!! json_encode($item->product->categories->pluck('name')) !!},
            'price': {{ $item->current_sale_price() }},
            'customer_id': {{ $cart->customer_id ?? 'null' }}
        };

        products.push(product);
      @endforeach

    dataLayer.push({
        'event': 'CartProducts',
        'cartProducts': products
    });
</script>
