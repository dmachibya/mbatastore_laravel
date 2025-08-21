@php
  $title_classes = isset($product) ? 'form-control' : 'form-control makeSlug';
@endphp
<div class="row">
  <div class="col-md-8">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">{{ isset($product) ? trans('app.update_product') : trans('app.add_product') }}</h3>
        <div class="box-tools pull-right">
          @if (!isset($product))
            <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.upload') }}" class="ajax-modal-btn btn btn-default btn-flat">{{ trans('app.bulk_import') }}</a>
          @endif
        </div>
      </div> <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('name', trans('app.form.name') . '*', ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_name') }}"></i>
              {!! Form::text('name', null, ['class' => $title_classes, 'placeholder' => trans('app.placeholder.title'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 nopadding-right">
            <div class="form-group">
              {!! Form::label('mpn', trans('app.form.mpn'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.mpn') }}"></i>
              {!! Form::text('mpn', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.mpn')]) !!}
            </div>
          </div>
          <div class="col-md-4 nopadding">
            <div class="form-group">
              {!! Form::label('gtin', trans('app.form.gtin'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.gtin') }}"></i>
              {!! Form::text('gtin', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.gtin')]) !!}
            </div>
          </div>
          <div class="col-md-4 nopadding-left">
            <div class="form-group">
              {!! Form::label('gtin_type', trans('app.form.gtin_type'), ['class' => 'with-help']) !!}
              {!! Form::select('gtin_type', $gtin_types, null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.gtin_type')]) !!}
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-{{ config('system_settings.show_item_conditions') ? 6 : 9 }} nopadding-right">
            <div class="form-group">
              {!! Form::label('sku', trans('app.form.sku') . '*', ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sku') }}"></i>
              {!! Form::text('sku', isset($inventory) ? $inventory->sku : null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.sku'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>
          </div>

          @if (config('system_settings.show_item_conditions'))
            <div class="col-md-3 nopadding">
              <div class="form-group">
                {!! Form::label('condition', trans('app.form.condition') . '*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_product_condition') }}"></i>
                {!! Form::select('condition', ['New' => trans('app.new'), 'Used' => trans('app.used'), 'Refurbished' => trans('app.refurbished')], isset($inventory) ? $inventory->condition : null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>
          @endif

          <div class="col-md-3 nopadding-left">
            <div class="form-group">
              {!! Form::label('active', trans('app.form.status') . '*', ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_inventory_status') }}"></i>
              {!! Form::select('active', ['1' => trans('app.active'), '0' => trans('app.inactive')], isset($inventory) ? $inventory->active : 1, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>

        @if (config('system_settings.show_item_conditions'))
          <div class="form-group">
            {!! Form::label('condition_note', trans('app.form.condition_note'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_condition_note') }}"></i>
            {!! Form::text('condition_note', isset($inventory) ? $inventory->condition_note : null, ['class' => 'form-control input-sm', 'placeholder' => trans('app.placeholder.condition_note')]) !!}
            <div class="help-block with-errors"></div>
          </div>
        @endif

        <fieldset>
          <legend>{{ trans('app.form.key_features') }}
            <button id="AddMoreField" class="btn btn-xs btn-new" data-toggle="tooltip" data-title="{{ trans('help.add_input_field') }}"><i class="fa fa-plus"></i></button>
          </legend>
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
        </fieldset>

        <div class="form-group">
          {!! Form::label('description', trans('app.form.description') . '*', ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_description') }}"></i>
          {!! Form::textarea('description', isset($inventory) ? $inventory->description : null, ['class' => 'form-control summernote', 'rows' => '4', 'placeholder' => trans('app.placeholder.description'), 'required']) !!}
          <div class="help-block with-errors">{!! $errors->first('description', ':message') !!}</div>
        </div>

        <fieldset>
          <legend>
            {{ trans('app.form.images') }}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_images') }}"></i>
          </legend>
          <div class="form-group">
            <div class="file-loading">
              <input id="dropzone-input" name="images[]" type="file" accept="image/*" multiple>
            </div>
            <span class="small"><i class="fa fa-info-circle"></i> {{ trans('help.multi_img_upload_instruction', ['size' => getAllowedMaxImgSize(), 'number' => getMaxNumberOfImgsForInventory(), 'dimension' => '800 x 800']) }}</span>
          </div>
        </fieldset>

        <fieldset>
          <legend>{{ trans('app.inventory_rules') }}</legend>
          <div class="row">
            <div class="col-md-6 nopadding-right">
              <div class="form-group">
                {!! Form::label('stock_quantity', trans('app.form.stock_quantity') . '*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.stock_quantity') }}"></i>
                {!! Form::number('stock_quantity', isset($inventory) ? $inventory->stock_quantity : 1, ['min' => 0, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="col-md-6 nopadding-left">
              <div class="form-group">
                {!! Form::label('min_order_quantity', trans('app.form.min_order_quantity'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.min_order_quantity') }}"></i>
                {!! Form::number('min_order_quantity', isset($inventory) ? $inventory->min_order_quantity : 1, ['min' => 1, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.min_order_quantity')]) !!}
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 nopadding-right">
              <div class="form-group">
                {!! Form::label('sale_price', trans('app.form.sale_price') . '*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sale_price') }}"></i>
                <div class="input-group">
                  @if (get_currency_prefix())
                    <span class="input-group-addon" id="basic-addon1">
                      {{ get_currency_prefix() }}
                    </span>
                  @endif

                  {!! Form::number('sale_price', isset($inventory) ? $inventory->sale_price : null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.sale_price'), 'required']) !!}

                  {{-- <input name="sale_price" value="{{ isset($inventory) ? $inventory->sale_price : Null }}" type="number" min="{{ $product->min_price }}" {{ $product->max_price ? ' max="'. $product->max_price .'"' : '' }} step="any" placeholder="{{ trans('app.placeholder.sale_price') }}" class="form-control" required="required"> --}}

                  @if (get_currency_suffix())
                    <span class="input-group-addon" id="basic-addon1">
                      {{ get_currency_suffix() }}
                    </span>
                  @endif
                </div>
                <div class="help-block with-errors"></div>
              </div>
            </div>
            <div class="col-md-6 nopadding-left">
              <div class="form-group">
                {!! Form::label('offer_price', trans('app.form.offer_price'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_price') }}"></i>
                <div class="input-group">
                  @if (get_currency_prefix())
                    <span class="input-group-addon" id="basic-addon1">
                      {{ get_currency_prefix() }}
                    </span>
                  @endif

                  {!! Form::number('offer_price', isset($inventory) ? $inventory->offer_price : null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.offer_price')]) !!}

                  @if (get_currency_suffix())
                    <span class="input-group-addon" id="basic-addon1">
                      {{ get_currency_suffix() }}
                    </span>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 nopadding-right">
              <div class="form-group">
                {!! Form::label('offer_start', trans('app.form.offer_start'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_start') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {!! Form::text('offer_start', isset($inventory) ? $inventory->offer_start : null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.offer_start')]) !!}
                </div>
                <div class="help-block with-errors"></div>
              </div>
            </div>

            <div class="col-md-6 nopadding-left">
              <div class="form-group">
                {!! Form::label('offer_end', trans('app.form.offer_end'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_end') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {!! Form::text('offer_end', isset($inventory) ? $inventory->offer_end : null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.offer_end')]) !!}
                </div>
                <div class="help-block with-errors"></div>
              </div>
            </div>
          </div>

          <div class="form-group">
            {!! Form::label('linked_items[]', trans('app.form.linked_items'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.inventory_linked_items') }}"></i>
            {!! Form::select('linked_items[]', $inventories, isset($inventory) ? unserialize($inventory->linked_items) : null, ['class' => 'form-control select2-normal', 'multiple' => 'multiple']) !!}
            <div class="help-block with-errors"></div>
          </div>
        </fieldset>

        <p class="help-block">* {{ trans('app.form.required_fields') }}</p>

        <div class="box-tools pull-right">
          {!! Form::submit(isset($product) ? trans('app.form.update') : trans('app.form.save'), ['class' => 'btn btn-flat btn-lg btn-primary']) !!}
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4 nopadding-left">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('app.organization') }}</h3>
      </div> <!-- /.box-header -->
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('available_from', trans('app.form.available_from'), ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.available_from') }}"></i>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            {!! Form::text('available_from', isset($inventory) ? $inventory->available_from : null, ['class' => 'datetimepicker form-control', 'placeholder' => trans('app.placeholder.available_from')]) !!}
          </div>
        </div>

        @if (is_incevio_package_loaded('pharmacy'))
          @include('pharmacy::inventory_form')
        @endif
        <div class="form-group">
          {!! Form::label('category_list[]', trans('app.form.categories') . '*') !!}
          {!! Form::select('category_list[]', $categories, null, ['class' => 'form-control select2-normal', 'multiple' => 'multiple', 'required']) !!}
          <div class="help-block with-errors"></div>
        </div>

        <fieldset>
          <legend>{{ trans('app.catalog_rules') }}</legend>

          <div class="form-group">
            <div class="input-group">
              {{ Form::hidden('requires_shipping', 0) }}
              {!! Form::checkbox('requires_shipping', null, !isset($product) ? 1 : null, ['id' => 'requires_shipping', 'class' => 'icheckbox_line requires_shipping']) !!}
              {!! Form::label('requires_shipping', trans('app.form.requires_shipping')) !!}
              <span class="input-group-addon" id="basic-addon1">
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.requires_shipping_with_inventory') }}"></i>
              </span>
            </div>
          </div>

          <div class="form-group">
            <div class="input-group">
              {{ Form::hidden('downloadable', 0) }}
              {!! Form::checkbox('downloadable', null, null, ['class' => 'icheckbox_line downloadable']) !!}
              {!! Form::label('downloadable', trans('app.form.downloadable')) !!}
              <span class="input-group-addon" id="basic-addon1">
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="{{ trans('help.downloadable') }}"></i>
              </span>
            </div>
          </div>

          <fieldset id="downloadable_section">
            {!! Form::hidden('stock_quantity', 1) !!}

            <ul class="mailbox-attachments clearfix pull-right">
              @if (isset($inventory))
                @foreach ($inventory->attachments as $attachment)
                  <li>
                    <div class="mailbox-attachment-info">
                      <a href="{{ route('attachment.download', $attachment) }}" class="mailbox-attachment-name"><i class="fa fa-file"></i> {{ $attachment->name }}</a>
                      <span class="mailbox-attachment-size">{{ get_formated_file_size($attachment->size) }}
                        <a href="{{ route('attachment.download', $attachment) }}" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                      </span>
                    </div>
                  </li>
                @endforeach
              @endif
            </ul>


            <div class="form-group">
              {!! Form::label('digital_file', trans('app.form.digital_file'), ['class' => 'with-help']) !!}
              <input type="file" name="digital_file" id="digital_file" class="form-control" required />
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
              {!! Form::label('download_limit', trans('app.form.download_limit'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.download_limit') }}"></i>
              {!! Form::number('download_limit', isset($inventory) ? $inventory->download_limit : null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.download_limit')]) !!}
              <div class="help-block with-errors"></div>
            </div>
          </fieldset>

          <fieldset id="form_shipping_section" class="form_shipping_section">
            <legend>{{ trans('app.shipping') }}</legend>
            <div class="form-group">
              <div class="input-group">
                {{ Form::hidden('free_shipping', 0) }}
                {!! Form::checkbox('free_shipping', null, null, ['id' => 'free_shipping', 'class' => 'icheckbox_line']) !!}
                {!! Form::label('free_shipping', trans('app.form.free_shipping')) !!}
                <span class="input-group-addon" id="">
                  <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.free_shipping') }}"></i>
                </span>
              </div>
            </div>

            <div class="form-group">
              {!! Form::label('warehouse_id', trans('app.form.warehouse'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_warehouse') }}"></i>
              {!! Form::select('warehouse_id', $warehouses, isset($warehouse) ? $warehouse->id : config('shop_settings.default_warehouse_id'), ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.select')]) !!}
            </div>

            <div class="form-group">
              {!! Form::label('shipping_weight', trans('app.form.shipping_weight'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.shipping_weight') }}"></i>
              <div class="input-group">
                {!! Form::number('shipping_weight', isset($inventory) ? $inventory->shipping_weight : null, ['class' => 'form-control', 'step' => 'any', 'min' => 0, 'placeholder' => trans('app.placeholder.shipping_weight')]) !!}
                <span class="input-group-addon">{{ config('system_settings.weight_unit') ?? 'gm' }}</span>
              </div>
              <div class="help-block with-errors"></div>
            </div>

            @if (is_incevio_package_loaded('packaging'))
              <div class="form-group">
                {!! Form::label('packaging_list[]', trans('packaging::lang.packagings'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_packagings') }}"></i>
                {!! Form::select('packaging_list[]', $packagings, isset($inventory) ? null : config('shop_settings.default_packaging_ids'), ['class' => 'form-control select2-normal', 'multiple' => 'multiple']) !!}
              </div>
            @endif
          </fieldset>

          <fieldset class="" id="attributesFieldset">
            @if (isset($attributes) && $attributes->count())
              @include('admin.product.inventory._attribute_dropdown', ['attributes' => $attributes])
            @endif
          </fieldset>

          <fieldset>
            <legend>
              {{ trans('app.featured_image') }}
              <i class="fa fa-question-circle small" data-toggle="tooltip" data-placement="top" title="{{ trans('help.product_featured_image') }}"></i>
            </legend>
            @if (isset($product) && $product->featureImage)
              <img src="{{ get_storage_file_url($product->featureImage->path, 'small') }}" alt="{{ trans('app.featured_image') }}">
              <label>
                <span style="margin-left: 10px;">
                  {!! Form::checkbox('delete_image[feature]', 1, null, ['class' => 'icheck']) !!} {{ trans('app.form.delete_image') }}
                </span>
              </label>
            @endif

            <div class="row">
              <div class="col-md-9 nopadding-right">
                <input id="uploadFile" placeholder="{{ trans('app.featured_image') }}" class="form-control" disabled="disabled" style="height: 28px;" />
              </div>
              <div class="col-md-3 nopadding-left">
                <div class="fileUpload btn btn-primary btn-block btn-flat">
                  <span>{{ trans('app.form.upload') }} </span>
                  <input type="file" name="images[feature]" id="uploadBtn" class="upload" />
                </div>
              </div>
            </div>
          </fieldset>

          <fieldset>
            <legend>{{ trans('app.branding') }}</legend>
            <div class="form-group">
              {!! Form::label('origin_country', trans('app.form.origin'), ['class' => 'with-help']) !!}
              {!! Form::select('origin_country', $countries, null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.origin')]) !!}
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
              {!! Form::label('manufacturer_id', trans('app.form.manufacturer'), ['class' => 'with-help']) !!}
              {!! Form::select('manufacturer_id', $manufacturers, null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.manufacturer')]) !!}
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
              {!! Form::label('brand', trans('app.form.brand'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.brand') }}"></i>
              {!! Form::text('brand', isset($inventory) ? $inventory->brand : null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.brand')]) !!}
            </div>

            <div class="form-group">
              {!! Form::label('model_number', trans('app.form.model_number'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.model_number') }}"></i>
              {!! Form::text('model_number', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.model_number')]) !!}
            </div>
          </fieldset>

          <fieldset>
            <legend>{{ trans('app.reporting') }}</legend>
            <div class="form-group">
              {!! Form::label('purchase_price', trans('app.form.purchase_price'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.purchase_price') }}"></i>
              <div class="input-group">
                @if (get_currency_prefix())
                  <span class="input-group-addon" id="basic-addon1">
                    {{ get_currency_prefix() }}
                  </span>
                @endif

                {!! Form::number('purchase_price', isset($inventory) ? $inventory->purchase_price : null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.purchase_price')]) !!}

                @if (get_currency_suffix())
                  <span class="input-group-addon" id="basic-addon1">
                    {{ get_currency_suffix() }}
                  </span>
                @endif
              </div>
            </div>
            <div class="form-group" id="supplier-form">
              {!! Form::label('supplier_id', trans('app.form.supplier'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_supplier') }}"></i>
              {!! Form::select('supplier_id', $suppliers, isset($inventory) ? null : config('shop_settings.default_supplier_id'), ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.select')]) !!}
            </div>
          </fieldset>

          <fieldset>
            <legend>{{ trans('app.seo') }}</legend>
            <div class="form-group">
              {!! Form::label('slug', trans('app.form.slug') . '*', ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.slug') }}"></i>
              {!! Form::text('slug', isset($inventory) ? $inventory->slug : null, ['class' => 'form-control slug', 'placeholder' => trans('app.placeholder.slug'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
              {!! Form::label('tag_list[]', trans('app.form.tags'), ['class' => 'with-help']) !!}
              {!! Form::select('tag_list[]', $tags, null, ['class' => 'form-control select2-tag', 'multiple' => 'multiple']) !!}
            </div>

            <div class="form-group">
              {!! Form::label('meta_title', trans('app.form.meta_title'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.meta_title') }}"></i>
              {!! Form::text('meta_title', isset($inventory) ? $inventory->meta_title : null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.meta_title')]) !!}
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group">
              {!! Form::label('meta_description', trans('app.form.meta_description'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.meta_description') }}"></i>
              {!! Form::text('meta_description', isset($inventory) ? $inventory->meta_description : null, ['class' => 'form-control', 'maxlength' => config('seo.meta.description_character_limit', '160'), 'placeholder' => trans('app.placeholder.meta_description')]) !!}
              <div class="help-block with-errors"><small><i class="fa fa-info-circle"></i> {{ trans('help.max_chat_allowed', ['size' => config('seo.meta.description_character_limit', '160')]) }}</small></div>
            </div>
          </fieldset>
        </fieldset>
      </div>
    </div>
  </div>
</div>
