@extends('admin.layouts.master')

@section('content')
    {!! Form::model($shop_translation, ['method' => 'POST', 'route' => ['admin.vendor.shop.translate.store', $shop], 'files' => true, 'id' => 'form-ajax-upload', 'data-toggle' => 'validator']) !!}
    
    @include('admin.shop._translation_form')
    
    {!! Form::close() !!}
@endsection