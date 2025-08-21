@extends('admin.layouts.master')

@section('content')
  {!! Form::model($category_group_translation, ['method' => 'POST', 'route' => ['admin.catalog.categoryGroup.translate.store', $category_group], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

  @include('admin.category.categoryGroup_translation_form')

  {!! Form::close() !!}
@endsection
