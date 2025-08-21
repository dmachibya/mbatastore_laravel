@foreach ($products as $item)
  <div class="best-seller-item border-animate">
    <div class="box-inner">
      <div class="best-seller-item-image">
        <a href="{{ route('show.product', $item->slug) }}">
          <img class="lazy" src="/images/square.webp" data-src="{{ get_inventory_img_src($item, 'medium') }}" data-name="product_image" alt="{{ $item->title }}" title="{{ $item->title }}">
        </a>
      </div> <!-- /.best-seller-item-image -->

      <div class="best-seller-item-details">
        <div class="best-seller-item-details-inner">
          <div class="best-seller-item-name">
            <a href="{{ route('show.product', $item->slug) }}">
              {{ $item->title }}
            </a>
          </div>

          <div class="best-seller-item-rating">
            @include('theme::partials._vertical_ratings', ['ratings' => $item->ratings])
          </div>

          <div class="best-seller-item-price">
            @include('theme::partials._home_pricing')
          </div>

          <div class="best-seller-item-utility">
            <div class="box-action-price mb-1">
              @include('theme::partials._home_pricing')
            </div>

            <div class="horizon-btns">
              @include('theme::partials._horizontal_action_buttons')
            </div>
          </div> <!-- /.best-seller-item-utility -->
        </div> <!-- /.best-seller-item-details-inner -->
      </div> <!-- /.best-seller-item-details -->
    </div> <!-- /.box-inner -->
  </div> <!-- /.best-seller-item -->
@endforeach
