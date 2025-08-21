@extends('admin.layouts.master')

@section('content')
  <div class="box">
    <div class="box-header">
        <div class="box-title">
          <h3>{{ trans('app.custom_shipping_labels') }}</h3>
        </div>
    </div>
    <div class="box-body">
          {!! Form::open(['route' => '', 'id' => 'form', 'class' => 'inline-form', 'data-toggle' => 'validator', 'files' => true]) !!}

          <div class="form-group">
            {!! Form::label('template', trans('app.select_template') . '*', ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_template') }}"></i>
            {!! Form::select('template', [null => trans('app.please_select')] + $templates, null, ['class' => 'form-control select2-normal', 'required']) !!}
            <div class="help-block with-errors"></div>
          </div>

          <div class="form-group">
            {!! Form::label('file', trans('app.file') . '*', ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.file') }}"></i>
            <div class="input-group">
              <span class="input-group-btn">
                <span class="btn btn-default btn-file">
                  {{ trans('app.browse') }} <input type="file" name="file" id="file" style="display: none;">
                </span>
              </span>
              <input type="text" class="form-control" readonly>
            </div>
            <div class="help-block with-errors"></div>
          </div>

          <div class="form-group">
            {!! Form::label('image_config', trans('app.image_config') . '*', ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.image_config') }}"></i>
            <div class="row">
              <div class="col-md-4">
                {!! Form::number('width', null, ['class' => 'form-control', 'placeholder' => trans('app.width')]) !!}
              </div>
              <div class="col-md-4">
                {!! Form::number('height', null, ['class' => 'form-control', 'placeholder' => trans('app.height')]) !!}
              </div>
              <div class="col-md-4">
                {!! Form::number('scale', null, ['class' => 'form-control', 'placeholder' => trans('app.scale')]) !!}
              </div>
            </div>
            <div class="help-block with-errors"></div>
          </div>

          {!! Form::button(trans('app.save'), ['type' => 'submit', 'class' => 'confirm btn btn-new btn-flat']) !!}
          {!! Form::close() !!}
    </div>
  </div>
@endSection