@extends('admin.layouts.master')

@section('content')
  {!! Form::open(['route' => 'admin.stock.inventory.store', 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

  @include('admin.inventory._form')

  {!! Form::close() !!}
@endsection

@section('page-script')
  @include('plugins.dropzone-upload')
  @include('plugins.dynamic-inputs')

  @if (is_incevio_package_loaded('wholesale'))
    @include('wholesale::scripts.wholesale_inventory_form_script')
  @endif

  @if (is_incevio_package_loaded('aiAssistant'))
    @include('aiAssistant::_generation_btn_script')
  @endif
@endsection
