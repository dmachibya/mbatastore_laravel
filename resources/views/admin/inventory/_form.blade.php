<div class="row">
  <div class="col-md-8">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">
          {{ isset($inventory) ? trans('app.update_inventory') : trans('app.add_inventory') }}
          @if ($product->downloadable)
            ({{ trans('app.digital_product') }})
          @endif
        </h3>
      </div> <!-- /.box-header -->
      <div class="box-body">
        @include('admin.partials._product_widget')

        @php
          if (isset($inventory)) {
              $product = $inventory->product;
          }

          $requires_shipping = $product->requires_shipping || (isset($inventory) && $inventory->product->requires_shipping);

          $title_classes = isset($inventory) ? 'form-control' : 'form-control makeSlug';
        @endphp

        {{ Form::hidden('product_id', $product->id) }}
        {{ Form::hidden('brand', $product->brand) }}

        <div class="row">
          <div class="col-md-8 nopadding-right">
            <div class="form-group">
              {!! Form::label('title', trans('app.form.title') . '*', ['class' => 'with-help']) !!}
              {!! Form::text('title', null, ['class' => $title_classes, 'placeholder' => trans('app.placeholder.title'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>
          </div>

          <div class="col-md-4 nopadding-left">
            <div class="form-group">
              {!! Form::label('available_from', trans('app.form.available_from'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.available_from') }}"></i>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                {!! Form::text('available_from', null, ['class' => 'datetimepicker form-control', 'placeholder' => trans('app.placeholder.available_from')]) !!}
              </div>
            </div>
          </div>

          <div class="col-md-{{ config('system_settings.show_item_conditions') ? 6 : 9 }} nopadding-right">
            <div class="form-group">
              {!! Form::label('sku', trans('app.form.sku') . '*', ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sku') }}"></i>
              {!! Form::text('sku', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.sku'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>
          </div>

          @if (config('system_settings.show_item_conditions'))
            <div class="col-md-3 nopadding">
              <div class="form-group">
                {!! Form::label('condition', trans('app.form.condition') . '*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_product_condition') }}"></i>
                {!! Form::select('condition', ['New' => trans('app.new'), 'Used' => trans('app.used'), 'Refurbished' => trans('app.refurbished')], isset($inventory) ? null : 'New', ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div>
            </div>
          @endif

          <div class="col-md-3 nopadding-left">
            <div class="form-group">
              {!! Form::label('active', trans('app.form.status') . '*', ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_inventory_status') }}"></i>
              {!! Form::select('active', ['1' => trans('app.active'), '0' => trans('app.inactive')], isset($inventory) ? null : 1, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div> <!-- /.row -->

        @if (config('system_settings.show_item_conditions'))
          <div class="form-group">
            {!! Form::label('condition_note', trans('app.form.condition_note'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_condition_note') }}"></i>
            {!! Form::text('condition_note', null, ['class' => 'form-control input-sm', 'placeholder' => trans('app.placeholder.condition_note')]) !!}
            <div class="help-block with-errors"></div>
          </div>
        @endif

        <div class="form-group">
          {!! Form::label('description', trans('app.form.description'), ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_description') }}"></i>

          @if (is_incevio_package_loaded('aiAssistant'))
            @include('aiAssistant::_generation_btn')
          @endif

          {!! Form::textarea('description', null, ['class' => 'form-control summernote', 'placeholder' => trans('app.placeholder.description')]) !!}
        </div>
      </div> <!-- /.box-body -->
    </div> <!-- /.box -->

    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('app.form.images') }}</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div> <!-- /.box-header -->
      <div class="box-body">
        <div class="form-group">
          <div class="file-loading">
            <input id="dropzone-input" name="images[]" type="file" accept="image/*" multiple>
          </div>
          <span class="small"><i class="fa fa-info-circle"></i> {{ trans('help.multi_img_upload_instruction', ['size' => getAllowedMaxImgSize(), 'number' => getMaxNumberOfImgsForInventory(), 'dimension' => '800 x 800']) }}</span>
        </div>
      </div> <!-- /.box-body -->
    </div> <!-- /.box -->

    @if (isset($inventoryVariant) && $inventoryVariant->count())
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('app.variants') }}</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

            {{-- <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.addVariant', $product) }}" class="ajax-modal-btn btn btn-xs btn-new" data-toggle="tooltip" data-title="{{ trans('app.add_variants') }}"><i class="fa fa-plus"></i></a> --}}
          </div>
        </div> <!-- /.box-header -->
        <div class="box-body">
          @include('admin.inventory._variants')
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    @endif

    @include('admin.inventory._key_features')

    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('app.seo') }}</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div> <!-- /.box-header -->
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('slug', trans('app.form.slug') . '*', ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.slug') }}"></i>
          {!! Form::text('slug', null, ['class' => 'form-control slug', 'placeholder' => trans('app.placeholder.slug'), 'required']) !!}
          <div class="help-block with-errors"></div>
        </div>

        <div class="form-group">
          {!! Form::label('tag_list[]', trans('app.form.tags'), ['class' => 'with-help']) !!}
          {!! Form::select('tag_list[]', $tags, null, ['class' => 'form-control select2-tag', 'multiple' => 'multiple']) !!}
          <div class="help-block with-errors"></div>
        </div>

        <div class="form-group">
          {!! Form::label('meta_title', trans('app.form.meta_title'), ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.meta_title') }}"></i>
          {!! Form::text('meta_title', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.meta_title')]) !!}
          <div class="help-block with-errors"></div>
        </div>

        <div class="form-group">
          {!! Form::label('meta_description', trans('app.form.meta_description'), ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.meta_description') }}"></i>
          {!! Form::text('meta_description', null, ['class' => 'form-control', 'maxlength' => config('seo.meta.description_character_limit', '160'), 'placeholder' => trans('app.placeholder.meta_description')]) !!}
          <div class="help-block with-errors"><small><i class="fa fa-info-circle"></i> {{ trans('help.max_chat_allowed', ['size' => config('seo.meta.description_character_limit', '160')]) }}</small></div>
        </div>
      </div> <!-- /.box-body -->
    </div> <!-- /.box -->

    {{-- div for buyerGroupDetails --}}
    @if (is_incevio_package_loaded('buyerGroup'))
      @include('buyerGroup::partials._inventory_buyerGroup_details')
    @endif

    <p class="help-block">* {{ trans('app.form.required_fields') }}</p>

    <div class="box">
      <div class="box-body">
        @if (isset($inventory))
          <a href="{{ route('admin.stock.inventory.index') }}" class="btn btn-default btn-flat">{{ trans('app.form.cancel_update') }}</a>
        @endif

        {!! Form::submit(trans('app.form.save'), ['class' => 'btn btn-flat btn-lg btn-new pull-right']) !!}
      </div> <!-- /.box-body -->
    </div> <!-- /.box -->
  </div><!-- /.col-md-8 -->

  <div class="col-md-4 nopadding-left">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('app.inventory_rules') }}</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div> <!-- /.box-header -->

      <div class="box-body">
        @if (is_incevio_package_loaded('auction'))
          @include('auction::admin._listing_type')
        @endif

        @if ($requires_shipping && !$product->downloadable)
          <div class="row">
            <div class="col-md-6 nopadding-right">
              <div class="form-group">
                {!! Form::label('stock_quantity', trans('app.form.stock_quantity') . '*', ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.stock_quantity') }}"></i>
                {!! Form::number('stock_quantity', isset($inventory) ? null : 1, ['min' => 0, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
                <div class="help-block with-errors"></div>
              </div> <!-- /.form-group -->
            </div> <!-- /.col-md-* -->

            <div class="col-md-6 nopadding-left">
              <div class="form-group">
                {!! Form::label('min_order_quantity', trans('app.form.min_order_quantity'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.min_order_quantity') }}"></i>
                {!! Form::number('min_order_quantity', isset($inventory) ? null : 1, ['min' => 1, 'class' => 'form-control', 'placeholder' => trans('app.placeholder.min_order_quantity')]) !!}
              </div> <!-- /.form-group -->
            </div> <!-- /.col-md-* -->
          </div> <!-- /.row -->
        @endif

        <div class="{{ isset($inventory) && $inventory->auctionable ? 'd-none' : '' }}" id="js-direct-sale">
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

                  {!! Form::number('sale_price', isset($inventory) ? $inventory->sale_price : null, ['class' => 'form-control', 'min' => $product->min_price, 'max' => $product->max_price ?? PHP_INT_MAX, 'step' => 'any', 'placeholder' => trans('app.placeholder.sale_price'), 'required']) !!}

                  {{-- <input name="sale_price" value="{{ isset($inventory) ? $inventory->sale_price : Null }}" type="number" min="{{ $product->min_price }}" {{ $product->max_price ? ' max="'. $product->max_price .'"' : '' }} step="any" placeholder="{{ trans('app.placeholder.sale_price') }}" class="form-control" required="required"> --}}

                  @if (get_currency_suffix())
                    <span class="input-group-addon" id="basic-addon1">
                      {{ get_currency_suffix() }}
                    </span>
                  @endif
                </div>
                <div class="help-block with-errors"></div>
              </div> <!-- /.form-group -->
            </div> <!-- /.col-md-* -->

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

                  {!! Form::number('offer_price', null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.offer_price')]) !!}

                  @if (get_currency_suffix())
                    <span class="input-group-addon" id="basic-addon1">
                      {{ get_currency_suffix() }}
                    </span>
                  @endif
                </div>
              </div> <!-- /.form-group -->
            </div> <!-- /.col-md-* -->
          </div> <!-- /.row -->

          <div class="row">
            <div class="col-md-6 nopadding-right">
              <div class="form-group">
                {!! Form::label('offer_start', trans('app.form.offer_start'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_start') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {!! Form::text('offer_start', null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.offer_start')]) !!}
                </div>
                <div class="help-block with-errors"></div>
              </div> <!-- /.form-group -->
            </div> <!-- /.col-md-* -->

            <div class="col-md-6 nopadding-left">
              <div class="form-group">
                {!! Form::label('offer_end', trans('app.form.offer_end'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_end') }}"></i>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  {!! Form::text('offer_end', null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.offer_end')]) !!}
                </div>
                <div class="help-block with-errors"></div>
              </div> <!-- /.form-group -->
            </div> <!-- /.col-md-* -->
          </div> <!-- /.row -->
        </div> <!-- /#js-direct-sale -->

        @if (is_incevio_package_loaded('auction'))
          @include('auction::admin._inventory_fields')
        @endif

        @if (is_incevio_package_loaded('wallet') && is_wallet_credit_reward_enabled())
          @include('wallet::admin._inventory_fields')
        @endif
      </div> <!-- /.box-body -->
    </div> <!-- /.box -->

    @if (is_incevio_package_loaded('wholesale'))
      @include('wholesale::wholesale_inventory_form')
    @endif

    @if (is_incevio_package_loaded('affiliate'))
      @include('affiliate::backend.affiliate_field')
    @endif

    @if (is_incevio_package_loaded('pharmacy'))
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('packages.pharmacy.pharmacy') }}</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div> <!-- /.box-header -->
        <div class="box-body">
          @include('pharmacy::inventory_form')
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    @endif

    @if ($product->downloadable)
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('app.downloadable') }}</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div> <!-- /.box-header -->
        <div class="box-body">
          {!! Form::hidden('stock_quantity', 1) !!}

          @if (isset($inventory))
            <ul class="list-group">
              @foreach ($inventory->attachments as $attachment)
                <li class="list-group-item">
                  <div class="mailbox-attachment-info">
                    <a href="{{ route('attachment.download', $attachment) }}" class="mailbox-attachment-name"><i class="fa fa-file"></i> {{ $attachment->name }}</a>

                    <span class="mailbox-attachment-size">{{ get_formated_file_size($attachment->size) }}
                      <a href="{{ route('attachment.download', $attachment) }}" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                    </span>
                  </div>
                </li>
              @endforeach
            </ul>
          @endif

          <div class="form-group">
            {!! Form::label('digital_file', trans('app.form.digital_file'), ['class' => 'with-help']) !!}
            <input type="file" name="digital_file" id="digital_file" class="form-control" {{ isset($inventory) && $inventory->attachments->count() ? '' : 'required' }} />
            <div class="help-block with-errors"></div>
          </div>

          <div class="form-group">
            {!! Form::label('download_limit', trans('app.form.download_limit'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.download_limit') }}"></i>
            {!! Form::number('download_limit', isset($inventory) ? $inventory->download_limit : null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.download_limit')]) !!}
            <div class="help-block with-errors"></div>
          </div>
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    @endif

    @if ($attributes->count())
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('app.attributes') }}</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div> <!-- /.box-header -->
        <div class="box-body">
          @foreach ($attributes as $attribute)
            <div class="form-group">
              {!! Form::label($attribute->name, $attribute->name . '*') !!}
              <select class="form-control select2" id="{{ $attribute->name }}" name="variants[{{ $attribute->id }}]" placeholder={{ trans('app.placeholder.select') }} required>
                <option value="">{{ trans('app.placeholder.select') }}</option>

                @foreach ($attribute->attributeValues as $attributeValue)
                  <option value="{{ $attributeValue->id }}" {{ isset($inventory) && count($inventory->attributes) && in_array($attributeValue->id, $inventory->attributeValues->pluck('id')->toArray()) ? 'selected' : '' }}>

                    {{ $attributeValue->value }}

                  </option>
                @endforeach
              </select>
              <div class="help-block with-errors"></div>
            </div> <!-- /.form-group -->
          @endforeach
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    @endif

    @if ($requires_shipping)
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('app.shipping') }}</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div> <!-- /.box-header -->

        <div class="box-body">
          @unless ($product->downloadable)
            <div class="form-group">
              {!! Form::label('warehouse_id[]', trans('app.form.warehouse'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_warehouse') }}"></i>
              {!! Form::select('warehouse_id[]', $warehouses, isset($inventory) ? null : config('shop_settings.default_warehouse_id'), ['class' => 'form-control select2-normal', 'multiple' => 'multiple']) !!}
            </div>

            <div class="form-group">
              {!! Form::label('shipping_weight', trans('app.form.shipping_weight'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.shipping_weight') }}"></i>
              <div class="input-group">
                {!! Form::number('shipping_weight', null, ['class' => 'form-control', 'step' => 'any', 'min' => 0, 'placeholder' => trans('app.placeholder.shipping_weight')]) !!}
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
          @endunless
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    @endif

    @include('admin.inventory._cross_selling_fields')

    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">{{ trans('app.reporting') }}</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div> <!-- /.box-header -->
      <div class="box-body">
        @isset($suppliers)
          <div class="form-group">
            {!! Form::label('supplier_id', trans('app.form.supplier'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_supplier') }}"></i>
            {!! Form::select('supplier_id', $suppliers, isset($inventory) ? null : config('shop_settings.default_supplier_id'), ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.select')]) !!}
          </div> <!-- /.form-group -->
        @endisset

        <div class="form-group">
          {!! Form::label('purchase_price', trans('app.form.purchase_price'), ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.purchase_price') }}"></i>
          <div class="input-group">
            @if (get_currency_prefix())
              <span class="input-group-addon" id="basic-addon1">
                {{ get_currency_prefix() }}
              </span>
            @endif

            {!! Form::number('purchase_price', null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.purchase_price')]) !!}

            @if (get_currency_suffix())
              <span class="input-group-addon" id="basic-addon1">
                {{ get_currency_suffix() }}
              </span>
            @endif
          </div> <!-- /.input-group -->
        </div> <!-- /.form-group -->
      </div> <!-- /.box-body -->
    </div> <!-- /.box -->
  </div><!-- /.col-md-4 -->
</div><!-- /.row -->
