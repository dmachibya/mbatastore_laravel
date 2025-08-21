<div class="modal-dialog modal-sm">
  <div class="modal-content">
    {!! Form::open(['route' => 'admin.account.shop.updatePayoutInstruction', 'id' => 'form', 'data-toggle' => 'validator']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      {{ trans('app.form.form') }}
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('payout_instruction', trans('app.form.payout_instruction'), ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ trans('help.shop_payout_instruction') }}"></i>

        {!! Form::textarea('payout_instruction', $payout_instruction, ['class' => 'form-control', 'id' => 'shop_payout_instruction', 'placeholder' => trans('help.shop_payout_instruction'), 'rows' => 5]) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>

    <div class="modal-footer">
      {!! Form::submit(trans('app.form.save'), ['class' => 'btn btn-flat btn-new']) !!}
    </div>
    {!! Form::close() !!}
  </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->
