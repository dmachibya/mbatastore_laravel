@if (count($trending_categories))
  <section>
    <div class="feature">
      <div class="container">
        <div class="feature-inner">
          <div class="feature-header">
            <div class="sell-header">
              <div class="sell-header-title">
                <h2 class="mr-3">
                  {!! trans('theme.trending_now') !!}
                  <i class="fal fa-fire-alt"></i>
                </h2>
              </div>

              <div class="feature-tabs">
                <ul class="flex-wrap">
                  @foreach ($trending_categories as $trendingCat)
                    <li class="{{ $loop->first ? 'active' : '' }} mb-md-0 mb-3">
                      <a href="#trending-{{ $trendingCat->slug }}">
                        {{ $trendingCat->name }}
                      </a>
                    </li>
                  @endforeach
                </ul>
              </div> <!-- /.feature-tabs -->
            </div> <!-- /.sell-header -->
          </div> <!-- /.feature-header -->

          <div class="feature-items">
            @foreach ($trending_categories as $trendingCat)
              <div class="feature-items-inner" id="trending-{{ $trendingCat->slug }}">
                @include('theme::partials._product_horizontal', ['products' => $trendingCat->listings])
              </div>
            @endforeach
          </div> <!-- /.feature-items -->
        </div>
      </div>
    </div>
  </section>
@endif
