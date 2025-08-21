@extends('admin.layouts.master')

@section('content')
  {!! Form::model($category_translation, ['method' => 'POST', 'route' => ['admin.catalog.category.translate.store', $category], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

  @include('admin.category.category_translation_form')

  {!! Form::close() !!}
@endsection
