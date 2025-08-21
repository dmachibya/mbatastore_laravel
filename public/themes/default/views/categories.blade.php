@extends('theme::layouts.main')

@section('content')
  <!-- HEADER SECTION -->
  <div class="container">
    <header class="page-header mt-3">
      <div class="row">
        <div class="col-md-12">
          <ol class="breadcrumb nav-breadcrumb">
            @include('theme::headers.lists.home')
            <li>@lang('theme.nav.categories')</li>
          </ol>
        </div>
      </div>
    </header>
  </div>

  <!-- CONTENT SECTION -->
  @include('theme::contents.categories_page-new')

  <!-- Recently Viewed -->
  @include('theme::sections.recent_views')
@endsection
