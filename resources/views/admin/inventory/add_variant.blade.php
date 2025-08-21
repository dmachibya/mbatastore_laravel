@extends('admin.layouts.master')

@section('content')
  <div class="row">
    <div class="col-md-8 nopadding-right">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('app.add_variants') }}</h3>
        </div> <!-- /.box-header -->
        <div class="box-body">
          {!! Form::open(['route' => ['admin.stock.inventory.saveVariant', $inventory], 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}

          <div class="row">
            <div class="col-md-4 nopadding-right">
              <div class="form-group">
                {!! Form::label('sku', trans('app.form.sku') . '*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sku') }}"></i>
                {!! Form::text('sku', isset($variant) ? $variant->sku : $inventory->sku . '-', ['class' => 'form-control', 'placeholder' => trans('app.placeholder.sku'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="col-md-4 nopadding-right nopadding-left">
              <div class="form-group">
                {!! Form::label('stock_quantity', trans('app.form.stock_quantity') . '*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.stock_quantity') }}"></i>
                {!! Form::number('stock_quantity', isset($variant) ? $variant->stock_quantity : $inventory->stock_quantity, ['min' => 0, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="col-md-4 nopadding-left">
              <div class="form-group">
                {!! Form::label('sale_price', trans('app.form.sale_price') . '*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sale_price') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon">{{ config('system_settings.currency_symbol') ?? '$' }}</span>
                  <input name="sale_price" value="{{ isset($variant) ? $variant->sale_price : $inventory->sale_price }}" type="number" step="any" placeholder="{{ trans('app.placeholder.sale_price') }}" class="form-control" required="required">
                </div>
                <div class="help-block with-errors"></div>
              </div>
            </div>
          </div>

          <fieldset>
            <legend> {{ trans('app.image') }} </legend>

            @if (isset($variant) && $variant->image)
              <img src="{{ get_storage_file_url($variant->image->path, 'small') }}" alt="{{ trans('app.variant_image') }}">
              <label>
                <span style="margin-left: 10px;">
                  {!! Form::checkbox('delete_image', 1, null, ['class' => 'icheck']) !!} {{ trans('app.form.delete_image') }}
                </span>
              </label>
            @endif

            <div class="row">
              <div class="col-md-9 nopadding-right">
                <input id="uploadFile" placeholder="{{ trans('app.variant_image') }}" class="form-control" disabled="disabled" style="height: 28px;" />
              </div>
              <div class="col-md-3 nopadding-left">
                <div class="fileUpload btn btn-primary btn-block btn-flat">
                  <span>{{ trans('app.form.upload') }} </span>
                  <input type="file" name="image" id="uploadBtn" class="upload" />
                </div>
              </div>
            </div>
          </fieldset>

          <div class="spacer30"></div>

          <fieldset>
            <legend>{{ trans('app.attributes') }}</legend>

            <div class="row">
              @foreach ($attributes as $attribute)
                @if ($productAttributeIds->contains($attribute->id))
                  <div class="col-md-6 nopadding-{{ $loop->iteration % 2 ? 'right' : 'left' }}">
                    <div class="form-group">
                      {!! Form::label($attribute->name, $attribute->name . '*', ['class' => 'with-help']) !!}

                      {!! Form::select('attributes[' . $attribute->id . ']', $attribute->attributeValues->pluck('value', 'id'), null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.attribute_values'), 'required']) !!}

                      {{-- <select class="form-control select2-normal" id="{{ $attribute->id }}" name="attributes[{{ $attribute->id }}]" placeholder="{{ trans('app.placeholder.attribute_values') }}" required="required">

                        <option value="">{{ trans('app.placeholder.select') }}</option>

                        @foreach ($attribute->attributeValues as $attributeValue)
                          <option value="{{ $attributeValue->id }}" {{ (old('attributes') != Null) && in_array($attributeValue->id, old('attributes')) ? "selected" : "" }}>
                            {{ $attributeValue->value }}
                          </option>
                        @endforeach
                      </select> --}}
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          </fieldset>

          <p class="help-block">* {{ trans('app.form.required_fields') }}</p>

          <a href="{{ route('admin.stock.product.edit', $product) }}" class="btn btn-default btn-lg">
            <i class="fa fa-angle-double-left"></i> {{ trans('app.back') }}
          </a>

          <div class="box-tools pull-right">
            {!! Form::submit(isset($product) ? trans('app.form.update') : trans('app.form.save'), ['class' => 'btn btn-flat btn-lg btn-primary']) !!}
          </div>

          {!! Form::close() !!}
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    </div><!-- /.col-md-8 -->

    <div class="col-md-4">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('app.product') }}</h3>
          <div class="box-tools pull-right">
          </div>
        </div> <!-- /.box-header -->
        <div class="box-body">

          @include('admin.partials._product_widget', ['product' => $product])

          <fieldset>
            <legend>{{ trans('app.variants') }}</legend>

            <table class="table table-default">
              @foreach ($product->inventories->whereNotNull('parent_id') as $variant)
                <tr>
                  <td>
                    @if ($variant->image)
                      <img src="{{ get_storage_file_url(optional($variant->image)->path, 'mini') }}" class="img-md" alt="{{ $variant->title }}">
                    @else
                      <img src="{{ url('images/placeholders/no_img.png') }}" class="img-md" alt="{{ $variant->title }}">
                    @endif
                  </td>

                  <td>
                    @foreach ($variant->attributeValues as $attrVal)
                      {{ $attrVal->value }}

                      @unless ($loop->last)
                        <span class="text-primary"> &#8226; </span>
                      @endunless
                    @endforeach
                  </td>
                </tr>
              @endforeach
            </table>
          </fieldset>
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    </div><!-- /.col-md-4 -->
  </div><!-- /.row -->
@endsection
