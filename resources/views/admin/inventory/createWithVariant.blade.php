@extends('admin.layouts.master')

@section('content')
  @can('view', $product)
    @include('admin.partials._product_widget')
  @endcan

  {!! Form::open(['route' => 'admin.stock.inventory.storeWithVariant', 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}

  {{-- @if (isset($product))
  {{ Form::hidden('product_id', $product->id) }}
@elseif($inventory)
  {{ Form::hidden('product_id', $inventory->product_id) }}
@endif --}}
  @if (isset($inventory))
    @php
      $product = $inventory->product;
    @endphp
  @endif

  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">{{ trans('app.add_inventory') }}</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div> <!-- /.box-header -->
    <div class="box-body">
      {{ Form::hidden('product', $product) }}

      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('title', trans('app.form.title') . '*') !!}
            {!! Form::text('title', null, ['class' => 'form-control makeSlug', 'placeholder' => trans('app.placeholder.title'), 'required']) !!}
            <div class="help-block with-errors"></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-6 nopadding-right">
          <div class="form-group">
            {!! Form::label('warehouse_id', trans('app.form.warehouse'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_warehouse') }}"></i>
            {!! Form::select('warehouse_id', $warehouses, null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.select')]) !!}
          </div>
        </div>

        <div class="col-lg-3 col-md-6 nopadding-left">
          @isset($suppliers)
            <div class="form-group">
              {!! Form::label('supplier_id', trans('app.form.supplier'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_supplier') }}"></i>
              {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.select')]) !!}
            </div>
          @endisset
        </div>

        <div class="col-lg-3 col-md-6 nopadding-right">
          <div class="form-group">
            {!! Form::label('available_from', trans('app.form.available_from'), ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.available_from') }}"></i>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
              {!! Form::text('available_from', null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.available_from')]) !!}
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 nopadding-left">
          <div class="form-group">
            {!! Form::label('active', trans('app.form.status') . '*', ['class' => 'with-help']) !!}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_inventory_status') }}"></i>
            {!! Form::select('active', ['1' => trans('app.active'), '0' => trans('app.inactive')], 1, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}
            <div class="help-block with-errors"></div>
          </div>
        </div>
      </div>

      @if ($product->requires_shipping)
        <div class="row">
          <div class="col-lg-3 col-md-6 nopadding-right">
            <div class="form-group">
              {!! Form::label('min_order_quantity', trans('app.form.min_order_quantity'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.min_order_quantity') }}"></i>
              {!! Form::number('min_order_quantity', isset($inventory) ? null : 1, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.min_order_quantity')]) !!}
            </div>
          </div>

          <div class="col-lg-3 col-md-6 nopadding-left">
            <div class="form-group">
              {!! Form::label('shipping_weight', trans('app.form.shipping_weight'), ['class' => 'with-help']) !!}
              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.shipping_weight') }}"></i>
              <div class="input-group">
                {!! Form::number('shipping_weight', null, ['class' => 'form-control', 'step' => 'any', 'placeholder' => trans('app.placeholder.shipping_weight')]) !!}
                <span class="input-group-addon">{{ config('system_settings.weight_unit') ?? 'gm' }}</span>
              </div>
              <div class="help-block with-errors"></div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 nopadding-right">
            <div class="form-group">
              <label class="with-help">&nbsp;</label>
              <div class="input-group">
                {{ Form::hidden('free_shipping', 0) }}
                {!! Form::checkbox('free_shipping', null, null, ['id' => 'free_shipping', 'class' => 'icheckbox_line']) !!}
                {!! Form::label('free_shipping', trans('app.form.free_shipping')) !!}
                <span class="input-group-addon" id="">
                  <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.free_shipping') }}"></i>
                </span>
              </div>
            </div>
          </div>

          @if (is_incevio_package_loaded('packaging'))
            <div class="col-lg-3 col-md-6 nopadding-left">
              <div class="form-group">
                {!! Form::label('packaging_list[]', trans('packaging::lang.packaging'), ['class' => 'with-help']) !!}
                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.select_packagings') }}"></i>
                {!! Form::select('packaging_list[]', $packagings, isset($inventory) ? null : config('shop_settings.default_packaging_ids'), ['class' => 'form-control select2-normal', 'multiple' => 'multiple']) !!}
              </div>
            </div>
          @endif
        </div>
      @endif

      @if (is_incevio_package_loaded('pharmacy'))
        @include('pharmacy::inventory_form')
      @endif

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
        {!! Form::textarea('description', null, ['class' => 'form-control summernote', 'placeholder' => trans('app.placeholder.description')]) !!}
      </div>
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->


  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">{{ trans('app.variants') }}</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div> <!-- /.box-header -->
    <div class="box-body">
      <table class="table table-default" id="variantsTable">
        <thead>
          <tr>
            <th>{{ trans('app.sl_number') }}</th>
            <th>{{ trans('app.form.variants') }}
              <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.variants') }}"><sup><i class="fa fa-question"></i></sup></small>
            </th>
            <th>{{ trans('app.form.image') }}
              <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.variant_image') }}"><sup><i class="fa fa-question"></i></sup></small>
            </th>
            <th>{{ trans('app.form.sku') }}
              <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sku') }}"><sup><i class="fa fa-question"></i></sup></small>
            </th>

            @if (config('system_settings.show_item_conditions'))
              <th>{{ trans('app.form.condition') }}
                <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.seller_product_condition') }}"><sup><i class="fa fa-question"></i></sup></small>
              </th>
            @endif

            <th>{{ trans('app.form.stock_quantity') }}
              <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.stock_quantity') }}"><sup><i class="fa fa-question"></i></sup></small>
            </th>
            <th>{{ trans('app.form.purchase_price') }}
              <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.purchase_price') }}"><sup><i class="fa fa-question"></i></sup></small>
            </th>
            <th>{{ trans('app.form.sale_price') }}
              <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.sale_price') }}"><sup><i class="fa fa-question"></i></sup></small>
            </th>
            <th>{{ trans('app.form.offer_price') }}
              <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_price') }}"><sup><i class="fa fa-question"></i></sup></small>
            </th>
            <th><i class="fa fa-trash-o"></i></th>
          </tr>
        </thead>
        <tbody style="zoom: 0.80;">
          @php
            $i = 0;
          @endphp
          @foreach ($combinations as $combination)
            <tr>
              <td>
                <div class="form-group">{{ $i + 1 }}</div>
              </td>
              <td>
                <div class="form-group">
                  @foreach ($combination as $attrId => $attrValue)
                    {{ Form::hidden('variants[' . $i . '][' . $attrId . ']', key($attrValue)) }}
                    {{ $attributes[$attrId] . ' : ' . current($attrValue) }}
                    {{ $attrValue !== end($combination) ? '; ' : '' }}
                  @endforeach
                </div>
              </td>
              <td>
                <div class="form-group">
                  <label class="img-btn-sm">
                    {{ Form::file('image[' . $i . ']') }}
                    <span>{{ trans('app.placeholder.image') }}</span>
                  </label>
                </div>
              </td>
              <td>
                <div class="form-group">
                  {!! Form::text('sku[' . $i . ']', null, ['class' => 'form-control sku', 'placeholder' => trans('app.placeholder.sku'), 'required']) !!}
                </div>
              </td>

              @if (config('system_settings.show_item_conditions'))
                <td>
                  <div class="form-group">
                    {!! Form::select('condition[' . $i . ']', ['New' => trans('app.new'), 'Used' => trans('app.used'), 'Refurbished' => trans('app.refurbished')], null, ['class' => 'form-control condition', 'required']) !!}
                  </div>
                </td>
              @endif

              <td>
                <div class="form-group">
                  {!! Form::number('stock_quantity[' . $i . ']', null, ['class' => 'form-control quantity', 'placeholder' => trans('app.placeholder.stock_quantity'), 'required']) !!}
                </div>
              </td>
              <td>
                <div class="form-group">
                  {!! Form::number('purchase_price[' . $i . ']', null, ['class' => 'form-control purchasePrice', 'step' => 'any', 'placeholder' => trans('app.placeholder.purchase_price')]) !!}
                </div>
              </td>
              <td>
                <div class="form-group">
                  {!! Form::number('sale_price[' . $i . ']', null, ['class' => 'form-control salePrice', 'step' => 'any', 'placeholder' => trans('app.placeholder.sale_price'), 'required']) !!}
                </div>
              </td>
              <td>
                <div class="form-group">
                  {!! Form::number('offer_price[' . $i . ']', null, ['class' => 'form-control offerPrice', 'step' => 'any', 'placeholder' => trans('app.placeholder.offer_price')]) !!}
                </div>
              </td>
              <td>
                <div class="form-group text-muted">
                  <i class="fa fa-close deleteThisRow" data-toggle="tooltip" data-placement="left" title="{{ trans('help.delete_this_combination') }}"></i>
                </div>
              </td>
            </tr>
            <?php $i++; ?>
          @endforeach
        </tbody>
      </table>
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->

  <fieldset id="offerDates" hidden>
    <legend>{{ trans('app.offer_dates') }}</legend>
    <div class="row">
      <div class="col-lg-3 col-md-6 nopadding-right">
        <div class="form-group">
          {!! Form::label('offer_start', trans('app.form.offer_start'), ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_start') }}"></i>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            {!! Form::text('offer_start', null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.offer_start')]) !!}
          </div>
          <div class="help-block with-errors"></div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 nopadding-left">
        <div class="form-group">
          {!! Form::label('offer_end', trans('app.form.offer_end'), ['class' => 'with-help']) !!}
          <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.offer_end') }}"></i>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            {!! Form::text('offer_end', null, ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.offer_end')]) !!}
          </div>
          <div class="help-block with-errors"></div>
        </div>
      </div>
    </div>
  </fieldset>

  @include('admin.inventory._key_features')

  @include('admin.inventory._cross_selling_fields')

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
        {!! Form::text('slug', null, ['class' => 'form-control slug', 'placeholder' => 'SEO Friendly URL', 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>

      <div class="form-group">
        {!! Form::label('tag_list[]', trans('app.form.tags'), ['class' => 'with-help']) !!}
        {!! Form::select('tag_list[]', $tags, null, ['class' => 'form-control select2-tag', 'multiple' => 'multiple']) !!}
      </div>

      <div class="form-group">
        {!! Form::label('meta_title', trans('app.form.meta_title'), ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.meta_title') }}"></i>
        {!! Form::text('meta_title', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.meta_title')]) !!}
      </div>

      <div class="form-group">
        {!! Form::label('meta_description', trans('app.form.meta_description'), ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.meta_description') }}"></i>
        {!! Form::text('meta_description', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.meta_description')]) !!}
      </div>
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->

  <p class="help-block">* {{ trans('app.form.required_fields') }}</p>

  <div class="box">
    <div class="box-body">
      {!! Form::submit(trans('app.form.save'), ['class' => 'btn btn-flat btn-lg btn-new pull-right']) !!}
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->
  {!! Form::close() !!}
@endsection

@section('page-script')
  @include('plugins.dynamic-inputs')

  <script language="javascript" type="text/javascript">
    ;
    (function($, window, document) {

      // Dynamically set get the value from row 1 and set to other rows
      $("#variantsTable > tbody tr:first input.sku").change(function() {
        var value = $(this).val();
        $('input.sku').each(function() {
          if ($(this).val() == '') $(this).val(value);
        })
      });

      $("#variantsTable > tbody tr:first select.condition").change(function() {
        var value = $(this).val();
        console.log(value);
        $('select.condition').each(function() {
          $(this).val(value);
        })
      });

      $("#variantsTable > tbody tr:first input.quantity").change(function() {
        var value = $(this).val();
        $('input.quantity').each(function() {
          if ($(this).val() == '') $(this).val(value);
        })
      });

      $("#variantsTable > tbody tr:first input.purchasePrice").change(function() {
        var value = $(this).val();
        $('input.purchasePrice').each(function() {
          if ($(this).val() == '') $(this).val(value);
        })
      });

      $("#variantsTable > tbody tr:first input.salePrice").change(function() {
        var value = $(this).val();
        $('input.salePrice').each(function() {
          if ($(this).val() == '') $(this).val(value);
        })
      });

      $("#variantsTable > tbody tr:first input.offerPrice").change(function() {
        var value = $(this).val();
        $('input.offerPrice').each(function() {
          if ($(this).val() == '') $(this).val(value);
        })
      });

      // Remove table rows
      $(".deleteThisRow").click(function(event) {
        $($(this).closest("tr")).remove();
        return false;
      });

      // Display Offer dates
      $('input.offerPrice').each(function() {
        if ($(this).val() != '') {
          $("#offerDates").show();
          $('#offer_start').attr('required', 'required');
          $('#offer_end').attr('required', 'required');
          return false;
        }
      });
      $(".offerPrice,.deleteThisRow").keyup(checkOfferPrice);
      $(".deleteThisRow").click(checkOfferPrice);

      function checkOfferPrice() {
        $('input[name^="offer_price"]').each(function() {
          if ($(this).val()) {
            $("#offerDates").show();
            $('#offer_start').attr('required', 'required');
            $('#offer_end').attr('required', 'required');
            return false;
          }
          $('#offer_start').removeAttr('required');
          $('#offer_end').removeAttr('required');
          $("#offerDates").hide();
        });
      }

      // Appy styleing for images upload button
      $("input:file").change(function() {

        if ($(this).val()) {
          // $(this).parent().append("<img src="+$(this).val()+" />");
          $(this).parent().css('background', '#dcdcdc');
        } else {
          $(this).parent().css('background', '#fff');
        }
      });
    }(window.jQuery, window, document));
  </script>
@endsection
