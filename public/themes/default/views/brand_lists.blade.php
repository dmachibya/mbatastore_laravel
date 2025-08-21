@extends('theme::layouts.main')

@section('content')
  <!-- BRAND COVER IMAGE -->
  <section class="brand-cover-img-wrapper mb-0">
    <div class="banner banner-o-hid cover-img-wrapper" style="background-image:url( {{ asset('images/placeholders/brand_cover.jpg') }} );">

      <div class="page-cover-caption">
        <h5 class="page-cover-title">{{ trans('theme.all_brands') }}</h5>
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
            <li>@lang('theme.brands')</li>
          </ol>
        </div>
      </div>
    </header>
  </div>

  <!-- CONTENT SECTION -->
  <section class="brand-list-area">
    <div class="container text-center mb-4 md-100">
      <div class="row thumb-lists justify-content-center align-self-center">
        @foreach ($brands as $brand)
          <div class="col-xl-3 col-md-4 col-sm-6 p-1">
            <div class="card-box text-center">
              <a href="{{ route('show.brand', $brand->slug) }}">
                <div class="thumb-lg d-flex thumbnail rounded-circle justify-content-center align-items-center mx-auto p-2">
                  <img class="lazy w-100" src="{{ get_storage_file_url(optional($brand->logoImage)->path, 'tiny_thumb') }}" data-src="{{ get_storage_file_url(optional($brand->logoImage)->path, 'full') }}" alt="{{ $brand->name }}">
                </div>

                <h4 class="mb-2">{{ $brand->name }}</h4>
              </a>
            </div> <!-- /.card-box -->
          </div> <!-- /.end col -->
        @endforeach
      </div><!-- /.row -->

      <div class="row d-flex justify-content-center pagenav-wrapper mt-5 mb-3">
        {{ $brands->links('theme::layouts.pagination') }}
      </div>
    </div><!-- /.container -->
  </section>

  <!-- BROWSING ITEMS -->
  @include('theme::sections.recent_views')
@endsection
