<script>
    dataLayer.push({
        'event': 'InitiateCheckout',
        'checkoutItems': {
            'item_count': '{{ $cart->inventories_count }}',
            'quantity': '{{ $cart->quantity }}',
            'subtotal': {{ number_format($cart->total, 2, '.', '') }},
            'total': {{ number_format($cart->calculate_grand_total(), 2, '.', '') }}
        }
    });
</script>
