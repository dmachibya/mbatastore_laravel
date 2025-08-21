@extends('admin.layouts.master')

@section('content')
  {!! Form::model($attribute_translation, ['method' => 'POST', 'route' => ['admin.catalog.attribute.translate.store', $attribute], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

  @include('admin.attribute._translation_form')

  {!! Form::close() !!}
@endsection