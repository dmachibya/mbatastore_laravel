@isset($linkable_items)
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">{{ trans('app.cross_selling') }}</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div> <!-- /.box-header -->
    <div class="box-body">
      <div class="form-group">
        {!! Form::label('linked_items[]', trans('app.form.linked_items'), ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.inventory_linked_items') }}"></i>
        {!! Form::select('linked_items[]', $linkable_items, isset($inventory) ? unserialize($inventory->linked_items) : null, ['class' => 'form-control select2-normal', 'multiple' => 'multiple']) !!}
        <div class="help-block with-errors"></div>
      </div> <!-- /.form-group -->
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->
@endisset
