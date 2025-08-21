@extends('theme::layouts.main')

@section('content')
  <!-- BRAND COVER IMAGE -->
  <section class="brand-cover-img-wrapper mb-0">
    <div class="banner banner-o-hid cover-img-wrapper" style="background-image:url( {{ asset('images/placeholders/shop_cover.jpg') }} );">

      <div class="page-cover-caption">
        <h5 class="page-cover-title shadow">{{ trans('theme.all_shops') }}</h5>
        {{-- <p class="page-cover-desc">{!! trans('theme.all_shops') !!}</p> --}}
      </div>
    </div>
  </section>

  <!-- HEADER SECTION -->
  <div class="container px-2">
    <header class="page-header">
      <div class="row">
        <div class="col-md-12">
          <ol class="breadcrumb nav-breadcrumb">
            @include('theme::headers.lists.home')
            <li>@lang('theme.vendors')</li>
          </ol>
        </div>
      </div>
    </header>
  </div>

  <!-- CONTENT SECTION -->
  <section>
    <div class="container mb-4 sm-100">
      <div class="row thumb-lists justify-content-center align-self-center">
        @foreach ($shops as $shop)
          <div class="col-lg-3 col-sm-6 p-1">
            <div class="card-box text-center">
              <a href="{{ route('show.store', $shop->slug) }}" class="text-reset">
                @if (config('system_settings.show_merchant_info_as_vendor'))
                  <div class="thumb-lg d-flex thumbnail rounded-circle justify-content-center align-items-center mx-auto p-2">
                    <img class="lazy w-100" src="{{ get_avatar_src($shop->owner, 'tiny_thumb') }}" data-src="{{ get_avatar_src($shop->owner, 'full') }}" alt="{{ $shop->name }}">
                  </div>

                  <h4 class="mb-2">{!! $shop->owner->getName() !!}</h4>
                @else
                  <div class="thumb-lg d-flex thumbnail rounded-circle justify-content-center align-items-center mx-auto p-2">
                    <img class="lazy w-100" src="{{ get_storage_file_url(optional($shop->logoImage)->path, 'tiny_thumb') }}" data-src="{{ get_storage_file_url(optional($shop->logoImage)->path, 'full') }}" alt="{{ $shop->name }}">
                  </div>

                  <h4 class="mb-1">
                    {!! $shop->getQualifiedName(10) !!}
                  </h4>

                  <h5 class="mb-3">
                    {!! $shop->reward_badge !!}
                  </h5>
                @endif
              </a>

              @include('theme::layouts.ratings', ['ratings' => $shop->ratings, 'count' => $shop->ratings_count])

              <div class="small">
                {{ trans('theme.member_since') . ' ' . $shop->created_at->toFormattedDateString() }}
              </div>

              {{-- <ul class="social-links list-inline my-3">
              <li class="list-inline-item"><a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Facebook"><i class="fa fa-facebook"></i></a></li>
              <li class="list-inline-item"><a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Twitter"><i class="fa fa-twitter"></i></a></li>
              <li class="list-inline-item"><a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Skype"><i class="fa fa-skype"></i></a></li>
            </ul> --}}

              <a href="{{ route('show.store', $shop->slug) }}" class="btn btn-default btn-rounded mt-3 waves-effect w-md waves-light">
                {{ trans('theme.visit_shop_page') }}
              </a>

              <div class="row mt-2">
                <div class="col-6">
                  <div class="mt-3">
                    <h4>{{ $shop->inventories_count }}</h4>
                    <p class="mb-0 text-muted">{{ trans('theme.active_listings') }}</p>
                  </div>
                </div>

                <div class="col-6">
                  <div class="mt-3">
                    <h4>{{ $shop->total_item_sold }}</h4>
                    <p class="mb-0 text-muted">{{ trans('theme.items_sold') }}</p>
                  </div>
                </div>
              </div><!-- /.row -->
            </div> <!-- /.card-box -->
          </div> <!-- /.end col -->
        @endforeach
      </div><!-- /.row -->

      <div class="row d-flex justify-content-center pagenav-wrapper mt-5 mb-3">
        {{ $shops->links('theme::layouts.pagination') }}
      </div><!-- /.row .pagenav-wrapper -->
    </div><!-- /.container -->
  </section>
  <!-- END CONTENT SECTION -->

  <!-- BROWSING ITEMS -->
  @include('theme::sections.recent_views')
@endsection
