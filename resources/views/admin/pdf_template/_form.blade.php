<div class="row">
  <div class="col-md-8 nopadding-right">
    <div class="form-group">
      {!! Form::label('name', trans('app.form.name') . '*', ['class' => 'with-help']) !!}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.name'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>

  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('type', trans('app.type') . '*', ['class' => 'with-help']) !!}
      {!! Form::select('type', $types, null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.type'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 nopadding-right">
    <div class="form-group">
      {!! Form::label('is_default', trans('app.is_default') . '*', ['class' => 'with-help']) !!}
      {!! Form::select('is_default', ['1' => trans('app.yes'), '0' => trans('app.no')], null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.is_default'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>

  <div class="col-md-6 nopadding-left">
    <div class="form-group">
      {!! Form::label('active', trans('app.form.status') . '*', ['class' => 'with-help']) !!}
      {!! Form::select('active', ['1' => trans('app.active'), '0' => trans('app.inactive')], null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.status'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

<div class="form-group">
  {!! Form::label('description', trans('app.form.description'), ['class' => 'with-help']) !!}
  {!! Form::textarea('description', null, ['class' => 'form-control summernote-without-toolbar', 'placeholder' => trans('app.placeholder.description'), 'rows' => '2']) !!}
</div>
