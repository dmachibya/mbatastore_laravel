<div class="modal-dialog modal-md">
  <div class="modal-content">
    {!! Form::open(['route' => 'admin.promotion.topBanner.update', 'method' => 'PUT', 'id' => 'form', 'files' => true, 'data-toggle' => 'validator']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      {{ trans('app.promo_banner') }}
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label for="exampleInputFile" class="with-help control-label"> {{ trans('app.top_bar_img') }}</label>

        @isset($top_bar_banner['img'])
          <div class="form-group text-center d-flex">
            <span>
              <img src="{{ get_storage_file_url($top_bar_banner['img'], 'full') }}" width="100%" class="popup-bg-img" alt="{{ trans('app.top_bar_img') }}">
            </span>
          </div>
        @endisset

        <div class="row">
          <div class="col-md-8 nopadding-right">
            @if($top_bar_banner)
              <input id="uploadFile2" placeholder="{{ get_storage_file_url($top_bar_banner['img'], 'full') ? $top_bar_banner['img'] : trans('app.top_bar_img') }}" class="form-control" disabled="disabled" style="height: 28px;" />
            @else
              <input id="uploadFile2" placeholder="{{ trans('app.top_bar_img') }}" class="form-control" disabled="disabled" style="height: 28px;" />
            @endif
            <div class="help-block with-errors">{{ trans('help.top_bar_img_size') }}</div>
          </div>

          <div class="col-md-4 nopadding-left">
            <div class="fileUpload btn btn-primary btn-block btn-flat">
              <span>{{ trans('app.form.upload') }}</span>
              <input type="file" name="top_bar_img" id="top_bar_img" class="upload" />
            </div>
          </div>
        </div> <!--/.row -->
      </div> <!--/.form-group -->

      <div class="form-group">
        {!! Form::label('action_url', trans('app.form.action_url')) !!}
        {!! Form::text('action_url', $top_bar_banner['action_url'] ?? null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.action_url')]) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>
    <div class="modal-footer">
      {!! Form::submit(trans('app.update'), ['class' => 'btn btn-flat btn-new']) !!}
    </div>
    {!! Form::close() !!}
  </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->
