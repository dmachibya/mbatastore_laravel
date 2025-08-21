@extends('admin.layouts.master')

@section('content')
  {!! Form::model($product_translation, ['method' => 'POST', 'route' => ['admin.catalog.product.translate.store', $product], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

  @include('admin.product._translation_form')

  {!! Form::close() !!}
@endsection