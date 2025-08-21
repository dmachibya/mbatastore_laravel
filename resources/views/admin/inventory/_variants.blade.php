<table class="table table-default" id="variantsTable">
  @foreach ($inventoryVariant as $variant)
    @if ($loop->first)
      <thead>
        <tr>
          <th>
            {{ trans('app.sl_number') }}
          </th>

          @foreach ($variant->attributes as $attr)
            <th>
              {{ $attr->name }}
            </th>
          @endforeach

          <th>
            {{ trans('app.form.image') }}
            {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.variant_image') }}"><sup><i class="fa fa-question"></i></sup></small> --}}
          </th>

          <th>
            {{ trans('app.form.sku') }}
            <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sku') }}"><sup><i class="fa fa-question"></i></sup></small>
          </th>

          <th>
            {{ trans('app.form.quantity') }}
            {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.stock_quantity') }}"><sup><i class="fa fa-question"></i></sup></small> --}}
          </th>

          <th>
            {{ trans('app.form.sale_price') }}
            <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sale_price') }}"><sup><i class="fa fa-question"></i></sup></small>
          </th>

          <th><i class="fa fa-trash-o"></i></th>
        </tr>
      </thead>

      <tbody style="zoom: 0.80;">
    @endif

    <tr class="variant-row">
      <td>
        <div class="form-group">{{ $loop->iteration }}</div>
      </td>

      @foreach ($variant->attributeValues as $attrVal)
        <td>
          {{ $attrVal->value }}
        </td>
      @endforeach

      <td>
        {{ Form::hidden('variant_ids[' . $variant->id . ']', $variant->id) }}

        <label class="img-btn-with-preview">
          {{ Form::file('variant_images[' . $variant->id . ']', ['class' => 'variant-img']) }}

          @if ($variant->image)
            <img src="{{ get_storage_file_url(optional($variant->image)->path, 'mini') }}" class="img-md" alt="{{ $variant->title }}">
          @else
            <img src="{{ url('images/placeholders/no_img.png') }}" class="img-md" alt="{{ $variant->title }}">
          @endif
        </label>
      </td>

      <td>
        <div class="form-group">
          {!! Form::text('variant_skus[' . $variant->id . ']', $variant->sku, ['class' => 'form-control variant-sku', 'placeholder' => trans('app.placeholder.sku'), 'required']) !!}
        </div>
      </td>

      <td>
        <div class="form-group">
          {!! Form::number('variant_quantities[' . $variant->id . ']', $variant->stock_quantity, ['class' => 'form-control variant-qtt', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
        </div>
      </td>

      <td>
        <div class="form-group">
          <div class="input-group">
            <span class="input-group-addon">{{ config('system_settings.currency_symbol') ?? '$' }}</span>
            {!! Form::number('variant_prices[' . $variant->id . ']', $variant->sale_price, ['class' => 'form-control variant-price', 'step' => 'any', 'placeholder' => trans('app.placeholder.sale_price'), 'required']) !!}
          </div>
        </div>
      </td>

      <td>
        <div class="form-group text-muted">
          <i class="fa fa-close deleteThisRow" data-toggle="tooltip" data-placement="top" title="{{ trans('help.delete_this_combination') }}"></i>
        </div>
      </td>
    </tr>

    @if ($loop->last)
      </tbody>
    @endif
  @endforeach
</table>

<p>
  <a href="{{ route('admin.stock.inventory.addVariant', $inventory) }}" class="btn btn-default">
    <i class="fa fa-plus"></i> {{ trans('app.add_new_variant') }}
  </a>

  {{-- <a href="{{ route('admin.catalog.product.addVariant', $product) }}" class="btn btn-default">
    <i class="fa fa-plus"></i> {{ trans('app.add_another_attribute') }}
  </a> --}}
</p>
