@extends('admin.layouts.master')

@section('content')
    {!! Form::model($manufacturer_translation, ['method' => 'POST', 'route' => ['admin.catalog.manufacturer.translate.store', $manufacturer], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}
    
    @include('admin.manufacturer._translation_form')
    
    {!! Form::close() !!}
@endsection