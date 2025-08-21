<div class="form-group">
  {!! Form::label('do_action', trans('app.form.type_dbreset')) !!}
  {!! Form::text('do_action', null, ['class' => 'form-control', 'required']) !!}
  <div class="help-block with-errors">{!! trans('help.type_dbreset') !!}</div>
</div>

<div class="form-group">
  {!! Form::label('password', trans('app.form.confirm_acc_password')) !!}
  {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => trans('app.placeholder.password'), 'data-minlength' => '6', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>
