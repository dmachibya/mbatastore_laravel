<script>
    var products = [];

      @foreach($wishlist as $wish)
        var product = {
            'sku': '{{ $wish->inventory->sku }}',
            'title': '{{ $wish->inventory->title }}',
            'category': {!! json_encode($wish->inventory->product->categories->pluck('name')) !!},
            'price': {{ $wish->inventory->current_sale_price() }},
            'customer_id': {{ $wish->inventory->customer_id ?? null }}
        };

        products.push(product);
      @endforeach

    dataLayer.push({
        'event': 'WishlistProducts',
        'wishlistProducts': products
    });
</script>
