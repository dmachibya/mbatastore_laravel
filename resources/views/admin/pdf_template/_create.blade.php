<div class="modal-dialog modal-md">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
      {{ trans('app.upload_template') }}
    </div>

    {!! Form::open(['route' => 'admin.utility.pdfTemplate.store', 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}
    <div class="modal-body">
      @include('admin.pdf_template._form')

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('template', trans('app.template'), ['class' => 'with-help']) !!}
            <div class="row">
              <div class="col-md-7 nopadding-right">
                <input id="uploadFile" placeholder="{{ trans('app.template') }}" class="form-control" disabled="disabled" style="height: 28px;" />
              </div>
              <div class="col-md-5 nopadding-left">
                <div class="fileUpload btn btn-primary btn-block btn-flat">
                  <span>{{ trans('app.form.upload') }}</span>
                  <input type="file" name="template" id="uploadBtn" class="upload" />
                </div>
              </div>
            </div>
          </div>

          <small class="help-block"><i class="fa fa-info-circle text-info"></i> {{ trans('app.form.file_extension_requirements', ['extension' => '.blade.php']) }}</small>
        </div>
      </div>

      <p class="help-block">* {{ trans('app.form.required_fields') }}</p>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-flat btn-new">{{ trans('app.form.update') }}</button>
    </div>
    {!! Form::close() !!}
  </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->";
