@extends('theme::layouts.main')

@section('content')
  <!-- HEADER SECTION -->
  <div class="container">
    <header class="page-header">
      <div class="row">
        <div class="col-md-12">
          <ol class="breadcrumb nav-breadcrumb">
            @include('theme::headers.lists.home')
            @include('theme::headers.lists.account')
            <li class="active">@lang('theme.' . $tab)</li>
          </ol>
        </div>
      </div>
    </header>
  </div>

  {{-- Notices --}}
  <div class="container">
    @if (!Auth::guard('customer')->user()->isVerified())
      <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

        <strong><i class="icon fas fa-info-circle"></i> {{ trans('theme.notice') }}</strong>

        {{ trans('messages.email_verification_notice') }}
        <a href="{{ route('customer.verify') }}"> {{ trans('auth.resend_verification_link') }}</a>
      </div>
    @endif
  </div>

  <!-- CONTENT SECTION -->
  <section class="account-section">
    <div class="container mt-3 mb-4 md-100">
      <div class="row">
        <div class="col-md-2 pr-0">

          @include('theme::nav.account_sidebar')

        </div><!-- /.col-md-2 -->

        <div class="col-md-10">
          @if (isset($content))
            {!! $content !!}
          @else
            @if ($tab == 'events')
              @if (is_incevio_package_loaded('eventy'))
                @include('eventy::frontend.customer_events')
              @endif
            @else
              @include('theme::contents.' . $tab)
            @endif
          @endif
        </div><!-- /.col-md-10 -->
      </div><!-- /.row -->
    </div><!-- /.container -->
  </section>

  <!-- BROWSING ITEMS -->
  @include('theme::sections.recent_views')
@endsection

@if (request()->is('*/wallet/deposit/form'))
  @section('scripts')
    @include('wallet::customer.scripts.deposit')
  @endsection
@endif
