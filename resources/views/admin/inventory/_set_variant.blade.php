<div class="modal-dialog modal-md">
  <div class="modal-content">
    {!! Form::open(['route' => ['admin.stock.inventory.addWithVariant', $product->id], 'method' => 'get', 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      {{ trans('app.form.set_variants') }}
    </div>

    <div class="modal-body">
      @foreach ($attributes as $attribute)
        <div class="form-group">
          {!! Form::label($attribute->name, $attribute->name, ['class' => 'with-help']) !!}*

          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.set_attribute') }}"></i>

          <select class="form-control select2-set_attribute" id="{{ $attribute->name }}" name="{{ $attribute->id }}[]" multiple='multiple' placeholder="{{ trans('app.placeholder.attribute_values') }}" required>
            @foreach ($attribute->attributeValues as $attributeValue)
              <option value="{{ $attributeValue->id }}">
                {{ $attributeValue->value }}
              </option>
            @endforeach
          </select>
          <div class="help-block with-errors"></div>
        </div> <!-- /.form-group -->
      @endforeach
      <p class="help-block">* {{ trans('app.form.required_fields') }}</p>
    </div> <!-- /.modal-body -->

    <div class="modal-footer">
      {!! Form::submit(trans('app.form.set_variants'), ['class' => 'btn btn-flat btn-lg btn-new pull-right']) !!}
    </div>
    {!! Form::close() !!}
  </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->
