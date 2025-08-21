@extends('admin.layouts.master')

@section('content')
  {!! Form::model($categorySubGroup_translation, ['method' => 'POST', 'route' => ['admin.catalog.categorySubGroup.translate.store', $categorySubGroup], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}

  @include('admin.category.subGroup._translation_form')

  {!! Form::close() !!}
@endsection