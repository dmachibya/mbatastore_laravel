<div class="product-info-rating">
  @for ($i = 0; $i < 5; $i++)
    @if ($ratings - $i >= 1)
      <span class="rated"><i class="fas fa-star"></i></span>
    @elseif($ratings - $i < 1 && $ratings - $i > 0)
      <span class="rated"><i class="fas fa-star-half-alt"></i></span>
    @else
      <span><i class="far fa-star"></i></span>
    @endif
  @endfor

  @if (isset($shop) && $shop && isset($count))
    <a href="javascript:void(0);" data-toggle="modal" data-target="#shopReviewsModal" data-tab="#shop_reviews_tab" class="shop-rating-count ml-1" aria-label="@lang('theme.reviews')">
      <span class="rating-count">
        {{ trans_choice('theme.reviews', $count, ['count' => $count]) }}
      </span>
    </a>
  @elseif(isset($item) && isset($count))
    <a href="{{ route('show.product', $item->slug) . '#reviews_tab' }}" class="rating-count product-rating-count ml-1" aria-controls="reviews_tab" role="tab" data-toggle="tab" id="js-open-product-reviews-tab" aria-label="@lang('theme.reviews')">
      ({{ get_formated_decimal($ratings, true, 1) }}) {{ trans_choice('theme.reviews', $count, ['count' => $count]) }}
    </a>
  @endif
</div>
