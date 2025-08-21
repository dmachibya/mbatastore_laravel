@extends('admin.layouts.master')

@section('content')
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-flash"></i> {{ trans('app.flashdeals') }}</h3>
      <div class="box-tools pull-right"></div>
    </div> <!-- /.box-header -->

    <div class="box-body">
      <div class="spacer20"></div>
      {!! Form::open(['route' => ['admin.flashdeal.create', 'flashdeal'], 'files' => false, 'class' => 'form-horizontal', 'id' => 'form', 'data-toggle' => '	validator']) !!}
      <div class="row">
        <div class="col-sm-10">
          <div class="form-group">
            <div class="col-sm-4 text-right">
              {!! Form::label('Start Time', trans('app.deal_schedule') . ':*', ['class' => 'with-help control-label']) !!}
              <p class="small text-muted">
                {!! trans('app.help_deal_schedule') !!}
              </p>
            </div>
            <div class="col-sm-4 nopadding-left">
              <input type="text" name="start_time" class="form-control datetimepicker" placeholder="{{ date('Y-m-d H:i a') }}" value="{{ $start_time ?? date('Y-m-d H:i a') }}" required>
              <div class="help-block with-errors">
                <small><i class="fa fa-clock-o"></i> {{ trans('app.starting_time') }}</small>
              </div>
            </div>
            <div class="col-sm-4 nopadding-left">
              <input type="text" name="end_time" class="form-control datetimepicker" placeholder="{{ date('Y-m-d H:i a') }}" value="{{ $end_time ?? date('Y-m-d H:i a') }}" required>
              <div class="help-block with-errors">
                <small><i class="fa fa-clock-o"></i> {{ trans('app.ending_time') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-10">
          <div class="form-group">
            <div class="col-sm-4 text-right">
              {!! Form::label('listings', trans('app.listings') . ':', ['class' => 'with-help control-label']) !!}
              <p class="small text-muted">
                {!! trans('app.help_deal_items') !!}
              </p>
            </div>
            <div class="col-sm-8 nopadding-left">
              {{-- {!! Form::select('listings[]', $listings ?? null, ['class' => 'form-control searchProductForSelect',
									'multiple' => 'multiple', 'placeholder' => trans('app.listings'), 'required']) !!} --}}
              <select style="width: 100%" multiple="multiple" name="listings[]" class="form-control searchInventoryForSelect">
                @if (!empty($listings))
                  @foreach ($listings as $key => $value)
                    <option value="{{ $key }}" selected>{{ $value }}</option>
                  @endforeach
                @endif
              </select>
              <div class="help-block with-errors">
                <small>{{ trans('app.hints_deal_items') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-10">
          <div class="form-group">
            <div class="col-sm-4 text-right">
              {!! Form::label('Featured', trans('app.featured_items') . ':', ['class' => 'with-help control-label']) !!}
              <p class="small text-muted">
                {!! trans('app.help_featured_items') !!}
              </p>
            </div>
            <div class="col-sm-8 nopadding-left">
              <select style="width: 100%" multiple="multiple" name="featured[]" class="form-control searchInventoryForSelect">
                @if (!empty($featured))
                  @foreach ($featured as $key => $value)
                    <option value="{{ $key }}" selected>{{ $value }}</option>
                  @endforeach
                @endif
              </select>
              <div class="help-block with-errors">
                <small>{{ trans('app.hints_featured_items') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-10">
          {!! Form::submit(trans('app.update'), ['class' => 'btn btn-lg btn-flat btn-new pull-right']) !!}
        </div>
      </div>
      {!! Form::close() !!}
      <div class="spacer20"></div>
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->
@endsection
