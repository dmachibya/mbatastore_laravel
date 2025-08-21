<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">{{ trans('app.key_features') }}</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div> <!-- /.box-header -->
  <div class="box-body">
    <div id="DynamicInputsWrapper">
      @if (isset($inventory) && $inventory->key_features)
        @foreach (unserialize($inventory->key_features) as $key_feature)
          <div class="form-group">
            <div class="input-group">
              {!! Form::text('key_features[]', $key_feature, ['class' => 'form-control input-sm', 'placeholder' => trans('app.placeholder.key_feature')]) !!}

              <span class="input-group-addon">
                <i class="fa fa-times removeThisInputBox" data-toggle="tooltip" data-title="{{ trans('help.remove_input_field') }}"></i>
              </span>
            </div>
          </div>
        @endforeach
      @else
        <div class="form-group">
          <div class="input-group">
            {!! Form::text('key_features[]', null, ['id' => 'field_1', 'class' => 'form-control input-sm', 'placeholder' => trans('app.placeholder.key_feature')]) !!}

            <span class="input-group-addon">
              <i class="fa fa-times removeThisInputBox" data-toggle="tooltip" data-title="{{ trans('help.remove_input_field') }}"></i>
            </span>
          </div>
        </div>
      @endif
    </div>

    <button id="AddMoreField" class="btn btn-default" data-toggle="tooltip" data-title="{{ trans('help.add_input_field') }}"><i class="fa fa-plus"></i> {{ trans('app.add_another_field') }}</button>
  </div> <!-- /.box-body -->
</div> <!-- /.box -->
