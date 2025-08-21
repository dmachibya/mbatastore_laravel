@php
  $translation = $manufacturer_translation->translation ?? null;
@endphp
{{ Form::hidden('lang', $selected_language) }}
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">
      {{ $manufacturer->hasTranslation($selected_language) ? trans('app.update_model_translation', ['model' => trans('app.model.menufacturer')]) : trans('app.add_model_translation', ['model' => trans('app.model.menufacturer')]) }}
      | {{ trans('app.name') . ':' }} {{ $manufacturer->name }}
    </h3>
  </div>
  <div class="box-body">
    <div class="row">
      <div class="col-md-12">
        <ul class="nav nav-tabs nav-tabs-justified mb-4">
          @foreach ($available_languages as $lang)
            <li class="col-md-{{ $loop->count > 1 ? 12 / $loop->count : 12 }} pr-0 pl-0 {{ $selected_language == $lang->code ? 'active' : '' }} text-center">
              <a href="{{ route('admin.catalog.manufacturer.translate.form', ['manufacturer' => $manufacturer, 'language' => $lang->code]) }}">
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
          <label for="name" class="with-help">{{ trans('app.name') }}</label>
          {{ Form::text('name', $translation['name'] ?? null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => trans('app.placeholder.title')]) }}
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

    <div class="box-tools pull-right">
      <button type="submit" class="btn btn-flat btn-lg btn-primary">{{ $manufacturer->hasTranslation($selected_language) ? trans('app.form.update') : trans('app.form.save') }}</button>
    </div>
  </div>
</div>
