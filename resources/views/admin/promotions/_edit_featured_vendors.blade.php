<div class="modal-dialog modal-md">
  <div class="modal-content">
    {!! Form::open(['route' => 'admin.update.featuredVendors', 'method' => 'PUT', 'id' => 'form', 'data-toggle' => 'validator']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      {{ trans('app.form.featured_vendors') }}
    </div>
    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('featured_vendors[]', trans('app.form.vendors')) !!}
        {!! Form::select('featured_vendors[]', $vendors, array_keys($featured_vendors), ['class' => 'form-control select2-normal', 'multiple' => 'multiple']) !!}
        <div class="help-block with-errors">{!! trans('help.featured_vendors') !!}</div>
      </div>
    </div>
    <div class="modal-footer">
      {!! Form::submit(trans('app.update'), ['class' => 'btn btn-flat btn-new']) !!}
    </div>
    {!! Form::close() !!}
  </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->
