<!-- a template for each modal -->
"<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="false">×</button>
        {{ trans('app.upload_template') }} <!--Insert Modal Header Here -->
      </div>
      <div class="modal-body">
      {!! Form::open(['route' => 'admin.shipping.label.upload', 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}
          <div class="form-group">
            <div class="input-group">
              <div class="custom-file">
                <input type="file" class="custom-file-input" name="template" id="shippingLabelTemplate" aria-describedby="fileHelp">
                <small class="help-block"><i class="fa fa-info-circle text-info"></i> {{ trans('app.form.file_extension_requirements',['extension' => '.blade.php']) }}</small>
              </div>
            </div>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-flat btn-new">{{ trans('app.form.upload') }}</button>
          </div>
        {!! Form::close() !!}
      </div>
      <div class="modal-footer">
     </div>
      </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->";