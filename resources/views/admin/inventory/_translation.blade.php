@extends('admin.layouts.master')

@section('content')
  {!! Form::model($inventory_translation, ['method' => 'POST', 'route' => ['admin.stock.inventory.translation.store', $inventory], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

  @include('admin.inventory._translation_form')

  {!! Form::close() !!}
@endsection