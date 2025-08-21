<ul class="sidebar-product-list">
  @foreach ($products as $item)
    <li>
      <div class="product-widget">
        <div class="product-img-wrap">
          <img class="product-img lazy" src="{{ get_inventory_img_src($item, 'tiny') }}" src="{{ get_inventory_img_src($item, 'small') }}" data-name="product_image" alt="{{ $item->title }}" title="{{ $item->title }}" />
        </div>

        <div class="product-info">
          @include('theme::layouts.ratings', ['ratings' => $item->feedbacks->avg('rating')])

          <a href="{{ route('show.product', $item->slug) }}" class="product-info-title">
            {{ $item->title }}
          </a>

          @include('theme::layouts.pricing', ['item' => $item])
        </div> <!-- /.product-info -->
      </div> <!-- /.product-widget -->
    </li>
  @endforeach
</ul>
