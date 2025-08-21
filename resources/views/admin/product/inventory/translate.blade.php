@extends('admin.layouts.master')

@section('content')
  {!! Form::model($product, ['method' => 'POST', 'route' => ['admin.stock.product.translate.store', $product], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

  @include('admin.product.inventory._form')

   {!! Form::close() !!}
@endsection