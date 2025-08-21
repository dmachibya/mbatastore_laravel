@extends('theme::layouts.main')

@section('content')
  <!-- breadcrumb -->
  @include('theme::headers.checkout_page')

  <!-- CONTENT SECTION -->
  @include('theme::contents.checkout_page')
@endsection

@section('scripts')
  @include('scripts.checkout')
  @include('theme::scripts.dynamic_checkout')
@endsection
