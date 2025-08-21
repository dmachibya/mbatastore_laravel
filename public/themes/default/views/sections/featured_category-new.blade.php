<section class="featured-category-section my-md-5 my-3">
  <div class="container">
    <div class="sell-header mb-4 mt-xl-0">
      <div class="sell-header-title">
        <h2>
          {{ trans('theme.featured_categories') }}
          {{-- <i class="fas fa-code-branch"></i> --}}
          <i class="fas fa-project-diagram"></i>
        </h2>
      </div>
      <div class="header-line">
        <span></span>
      </div>
    </div> <!-- /.sell-header -->

    <div class="featured-categories owl-carousel">
      @foreach ($featured_category as $item)
        <div class="featured-category">
          <a href="{{ route('category.browse', $item->slug) }}">
            <figure>
              <img class="lazy" src="{{ get_storage_file_url(optional($item->featureImage)->path, 'tiny') }}" data-src="{{ get_storage_file_url(optional($item->featureImage)->path, 'full') }}" alt="{{ $item->name }}">
            </figure>

            <div class="featured-category-text py-2">
              <h3>{{ $item->name }}</h3>
              <span> {{ trans('theme.listings_count', ['count' => $item->listings_count]) }}</span>
            </div>
          </a>
        </div> <!-- /.featured-category -->
      @endforeach
    </div> <!-- /.featured-categories -->
  </div>
</section>
