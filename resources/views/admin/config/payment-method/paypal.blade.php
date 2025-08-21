<div class="modal-dialog modal-sm">
  <div class="modal-content">
    {!! Form::model($paypal, ['method' => 'PUT', 'route' => ['admin.setting.paypal.update', $paypal], 'id' => 'form', 'data-toggle' => 'validator']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      {{ trans('app.form.config') . ' paypal' }}
    </div>
    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('sandbox', trans('app.form.environment') . '*', ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.config_payment_environment') }}"></i>
        {!! Form::select('sandbox', ['1' => trans('app.test'), '0' => trans('app.live')], null, ['class' => 'form-control select2-normal', 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>

      <div class="form-group">
        {!! Form::label('client_id', trans('app.form.paypal_client_id') . '*', ['class' => 'with-help']) !!}
        {{-- <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.config_consumer_key') }}"></i> --}}
        {!! Form::text('client_id', null, ['class' => 'form-control', 'placeholder' => trans('app.form.paypal_client_id'), 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>

      <div class="form-group">
        {!! Form::label('secret', trans('app.form.paypal_secret') . '*', ['class' => 'with-help']) !!}
        {!! Form::text('secret', null, ['class' => 'form-control', 'placeholder' => trans('app.form.paypal_secret'), 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>

    </div>
    <div class="modal-footer">
      {!! Form::submit(trans('app.update'), ['class' => 'btn btn-flat btn-new']) !!}
    </div>
    {!! Form::close() !!}
  </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->
