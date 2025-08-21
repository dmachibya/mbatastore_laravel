@extends('admin.layouts.master')

@php
  $can_update = Gate::allows('update', $config) ?? null;
  $active_shipping_methods = $config->shippingMethods->pluck('id')->toArray();
  $has_config = false;
@endphp

@section('content')
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">
        {{ trans('app.shipping_methods') }}
      </h3>
    </div> <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-12">
          @unless (count($active_shipping_methods))
              <div class="text-center">
                <p class="lead">{{ trans('app.no_shipping_methods_are_configured') }}</p>
              </div>
          @endunless
          @foreach ($shipping_method_types as $type_id => $type)
            @php
              $shipping_providers = $shipping_methods->where('type', $type_id)->where('enabled', 1);
              $logo_path = sys_image_path('shipping-method-types') . "{$type_id}.svg";
            @endphp

            {{-- @if ($shipping_providers->count()) --}}
            <div class="row">
              <span class="spacer10"></span>
              <div class="col-sm-5">
                @if (File::exists($logo_path))
                  <img src="{{ asset($logo_path) }}" width="100" height="25" alt="{{ $type }}">
                  <span class="spacer10"></span>
                @else
                  <p class="lead">{{ $type }}</p>
                @endif
                <p>{!! get_shipping_method_type($type_id)['description'] !!}</p>
              </div> <!-- /.col-ms-5 -->

              <div class="col-sm-7">
                @foreach ($shipping_providers as $shipping_provider)
                  @php
                    $has_config = false;
                    $logo_path = sys_image_path('shipping-methods') . "{$shipping_provider->code}.png";
                  @endphp
                  <ul class="list-group">
                    <li class="list-group-item">
                      @if (File::exists($logo_path))
                        <img src="{{ asset($logo_path) }}" class="open-img-md" alt="{{ $type }}">
                      @else
                        <p class="list-group-item-heading inline lead">
                          {{ $shipping_provider->name }}
                        </p>
                      @endif

                      <span class="spacer10"></span>

                      <p class="list-group-item-text">
                        {!! $shipping_provider->description !!}
                      </p>

                      <span class="spacer20"></span>

                      @if (in_array($shipping_provider->id, $active_shipping_methods))
                        @if ($can_update)
                          @switch ($shipping_provider->code)
                            @case ('shippo')
                              @php
                                $has_config = true;
                              @endphp
                            @break
                          @endswitch

                          @unless ($has_config)
                            <div class="alert alert-danger">@lang('app.payment_method_configuration_issue')</div>
                          @endunless

                          @if ($shipping_provider->code == 'shippo')
                            <a href="javascript:void(0)" data-link="{{ route('admin.setting.shippingMethod.activate', $shipping_provider->id) }}" class="btn ajax-modal-btn btn-info">{{ trans('app.update') }}</a>
                          @else
                            <a href="javascript:void(0)" data-link="{{ route('admin.setting.shippingMethod.activate', $shipping_provider->id) }}" class="btn ajax-modal-btn btn-info">{{ trans('app.update') }}</a>
                          @endif

                          <a href="{{ route('admin.setting.shippingMethod.deactivate', $shipping_provider->id) }}" class="btn btn-default ajax-silent confirm"> {{ trans('app.deactivate') }}</a>
                        @else
                          <span class="label label-default">{{ trans('app.active') }}</span>
                        @endif
                      @else
                        @if ($can_update)
                          <a href="javascript:void(0)" data-link="{{ route('admin.setting.shippingMethod.activate', $shipping_provider->id) }}" class="btn ajax-modal-btn btn-primary">{{ $has_config ? trans('app.reactivate') : trans('app.activate') }}</a>
                        @else
                          <span class="label label-default">{{ trans('app.inactive') }}</span>
                        @endif
                      @endif

                      <span class="spacer15"></span>
                    </li>
                  </ul>
                @endforeach
              </div> <!-- /.col-ms-7 -->
            </div> <!-- /.row -->

            @unless ($loop->last)
              <hr />
            @endunless
            {{-- @endif --}}
          @endforeach
        </div> <!-- col-sm-12 -->
      </div> <!-- row -->
    </div> <!-- box-body -->
  </div> <!-- box -->
@endsection
