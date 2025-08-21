@extends('admin.layouts.master')

@section('content')
  <div class="alert alert-danger">
    <strong><i class="icon fa fa-info-circle"></i>{{ trans('app.notice') }}</strong>
    {{ trans('messages.import_ignored') }}
  </div>
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">
        {{ trans('app.import_failed') }} <small>({{ trans('app.total_number_of_rows', ['value' => count($failed_rows)]) }})</small>
      </h3>
    </div> <!-- /.box-header -->

    <div class="box-body responsive-table">
      <table class="table table-striped">
        <thead>
        <tr>
            <th>{{ trans('app.slug') }}</th>
            <th>{{ trans('app.language') }}</th>
            <th>{{ trans('app.name') }}</th>
            <th>{{ trans('app.brand') }}</th>
            <th>{{ trans('app.description') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($rows as $row)
            <tr>
            <td> {{ $row['slug'] }} </td>
            <td>{{ $row['lang'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td>{{ $row['brand'] }}</td>
            <td>{{ $row['description'] }}</td>
            </tr>
        @endforeach
        </tbody>
      </table>
    </div> <!-- /.box-body -->

    <div class="box-footer">
      <small class="indent20">{{ trans('app.total_number_of_rows', ['value' => count($failed_rows)]) }}</small>
      <div class="box-tools pull-right">
        {!! Form::open(['route' => 'admin.catalog.product.translate.download.failedRows', 'id' => 'form', 'class' => 'inline-form', 'data-toggle' => 'validator']) !!}
        @foreach ($failed_rows as $row)
          <input type="hidden" name="data[]" value="{{ serialize($row['data']) }}">
        @endforeach
        {!! Form::button(trans('app.download_failed_rows'), ['type' => 'submit', 'class' => 'btn btn-new btn-flat']) !!}
        {!! Form::close() !!}
      </div>
    </div> <!-- /.box-footer -->
  </div> <!-- /.box -->
@endsection
