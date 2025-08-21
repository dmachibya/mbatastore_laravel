@php
  $translation = $inventory_translation->translation ?? null;
@endphp
{{ Form::hidden('lang', $selected_language) }}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">
          {{ $inventory->hasTranslation($selected_language) ? trans('app.update_model_translation', ['model' => trans('app.model.inventory')]) : trans('app.add_model_translation', ['model' => trans('app.model.inventory')]) }}
          | {{ trans('app.title') . ':' }} {{ $inventory->title }}
        </h3>
        <div class="box-tools pull-right">
          <a href="javascript:void(0)" data-link="{{ route('admin.stock.inventory.translate.bulk') }}" class="ajax-modal-btn btn btn-default btn-flat"><em class="fa fa-language"></em> {{ trans('app.bulk_translation_import') }}</a>
        </div>
      </div> {{-- box header --}}
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <ul class="nav nav-tabs nav-tabs-justified mb-4">
              @foreach ($available_languages as $lang)
                <li class="col-md-{{ $loop->count > 1 ? 12 / $loop->count : 12 }} pr-0 pl-0 {{ $selected_language == $lang->code ? 'active' : '' }} text-center">
                  <a href="{{ route('admin.stock.inventory.translation.form', ['inventory' => $inventory, 'language' => $lang->code]) }}">
                    {!! get_flag_img_by_code(array_slice(explode('_', $lang->php_locale_code), -1)[0]) !!}
                    {{ $lang->language }}</a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="title" class="with-help">{{ trans('app.title') }}</label>
              {{ Form::text('title', $translation['title'] ?? null, ['id' => 'title', 'class' => 'form-control', 'placeholder' => trans('app.placeholder.title')]) }}
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="description" class="with-help">{{ trans('app.form.description') }}</label>
          <em class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_description') }}"></em>
          {{ Form::textarea('description', $translation['description'] ?? null, ['id' => 'description', 'class' => 'form-control summernote', 'rows' => 8, 'placeholder' => trans('app.placeholder.description')]) }}
          <div class="help-block with-errors">{{ $errors->first('description', ':message') }}</div>
        </div>

        @if (config('system_settings.show_item_conditions'))
          <div class="form-group">
            {!! Form::label('condition_note', trans('app.form.condition_note'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_condition_note') }}"></i>
            {!! Form::text('condition_note', $translation['condition_note'] ?? null, ['class' => 'form-control input-sm', 'placeholder' => trans('app.placeholder.condition_note')]) !!}
            <div class="help-block with-errors"></div>
          </div>
        @endif
        
        <div class="form-group">
          <label for="DynamicIntputsWrapper">{{ trans('app.key_features') }}</label>
          <div>
            <div id="DynamicInputsWrapper">
              @if (isset($translation['key_features']))
                @foreach ($translation['key_features'] as $key_feature)
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
          </div> 
        </div><!-- /.box for key_features-->

        <div class="box-tools pull-right">
          <button type="submit" class="btn btn-flat btn-lg btn-primary">{{ $inventory->hasTranslation($selected_language) ? trans('app.form.update') : trans('app.form.save') }}</button>
        </div>
      </div>
    </div>
  </div>
</div>

@section('page-script')
  @include('plugins.dynamic-inputs')
@endsection
