@php
  $translation = $categorySubGroup_translation->translation ?? null;
@endphp
{{ Form::hidden('lang', $selected_language) }}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">
          {{ $categorySubGroup->hasTranslation($selected_language) ? trans('app.update_model_translation', ['model' => trans('app.model.category_sub_group')]) : trans('app.add_model_translation', ['model' => trans('app.model.category_sub_group')]) }}
          | {{ trans('app.name') . ':' }} {{ $categorySubGroup->name }}
        </h3>
      </div> {{-- box header --}}
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <ul class="nav nav-tabs nav-tabs-justified mb-4">
              @foreach ($available_languages as $lang)
                <li class="col-md-{{ $loop->count > 1 ? 12 / $loop->count : 12 }} pr-0 pl-0 {{ $selected_language == $lang->code ? 'active' : '' }} text-center">
                  <a href="{{ route('admin.catalog.categorySubGroup.translate.form', ['categorySubGroup' => $categorySubGroup, 'language' => $lang->code]) }}">
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
        <div class="box-tools pull-right">
          <button type="submit" class="btn btn-flat btn-lg btn-primary">{{ $categorySubGroup->hasTranslation($selected_language) ? trans('app.form.update') : trans('app.form.save') }}</button>
        </div>
      </div>
    </div>
  </div>
</div>
