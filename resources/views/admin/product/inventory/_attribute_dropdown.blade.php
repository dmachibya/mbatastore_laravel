@foreach ($attributes as $attribute)
  <div class="form-group">
    {!! Form::label($attribute->name, $attribute->name . '*') !!}
    <select class="form-control select2-set_attribute" id="{{ $attribute->id }}" name="{{ $attribute->name }}[]" multiple="multiple" placeholder={{ trans('app.placeholder.select') }} style="width: 100%;" required>
      <option value="">{{ trans('app.placeholder.select') }}</option>

      @foreach ($attribute->attributeValues as $attributeValue)
        <option value="{{ $attributeValue->id }}" {{ isset($inventory) && count($inventory->attributes) && in_array($attributeValue->id, $inventory->attributeValues->pluck('id')->toArray()) ? 'selected' : '' }}>

          {{ $attributeValue->value }}
        </option>
      @endforeach
    </select>
  </div>
@endforeach
