<section>
  <div class="product-cat">
    <div class="container p-0">
      <div class="product-cat-inner">
        <div class="product-cat-list">
          <div class="row">
            @foreach ($featured_category as $item)
              <div class="col-4 col-md-2 col-sm-3 px-2">
                <div class="product-cat-list-item py-2">
                  <a href="{{ route('category.browse', $item->slug) }}">
                    <img class="lazy" src="{{ get_storage_file_url(optional($item->featureImage)->path, 'tiny') }} }}" data-src="{{ get_storage_file_url(optional($item->featureImage)->path, 'full') }} }}" alt="{{ $item->name }}">
                  </a>

                  <a class="product-cat-list-text my-2" href="{{ route('category.browse', $item->slug) }}">{{ $item->name }}</a>
                </div> <!-- /.product-cat-list-item -->
              </div> <!-- /.col-6 -->
            @endforeach
          </div> <!-- /.row -->
        </div> <!-- /.product-cat-list -->
      </div> <!-- /.product-cat-inner -->
    </div> <!-- /.container -->
  </div> <!-- /.product-cat -->
</section>
