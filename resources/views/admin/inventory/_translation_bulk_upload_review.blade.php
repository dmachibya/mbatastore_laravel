@extends('admin.layouts.master')

@section('content')
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">
        {{ trans('app.model_translations_bulk_upload', ['model' => trans('app.model.inventory')]) }} {{ trans('app.preview') }} <small>({{ trans('app.total_number_of_rows', ['value' => count($rows)]) }})</small>
      </h3>
    </div> <!-- /.box-header -->

    <div class="box-body responsive-table">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>{{ trans('app.slug') }}</th>
            <th>{{ trans('app.language') }}</th>
            <th>{{ trans('app.title') }}</th>
            <th>{{ trans('app.description') }}</th>
            <th>{{ trans('app.key_features') }}</th>
            <th>{{ trans('app.condition_note') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($rows as $row)
            <tr>
              <td> {{ $row['slug'] }} </td>
              <td>{{ $row['lang'] }}</td>
              <td>{{ $row['title'] }}</td>
              <td>{{ $row['description'] }}</td>
              <td>{{ $row['key_features'] }}</td>
              <td>{{ $row['condition_note'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div> <!-- /.box-body -->

    <div class="box-footer">
      <a href="" class="btn btn-default btn-flat">{{ trans('app.cancel') }}</a>
      <small class="indent20">{{ trans('app.total_number_of_rows', ['value' => count($rows)]) }}</small>
      <div class="box-tools pull-right">
        {!! Form::open(['route' => 'admin.stock.inventory.translate.bulk.import', 'id' => 'form', 'class' => 'inline-form', 'data-toggle' => 'validator']) !!}
        @foreach ($rows as $row)
          {{ Form::hidden('data[]', serialize($row)) }}
        @endforeach
        {!! Form::button(trans('app.looks_good'), ['type' => 'submit', 'class' => 'confirm btn btn-new btn-flat']) !!}
        {!! Form::close() !!}
      </div>
    </div> <!-- /.box-footer -->
  </div> <!-- /.box -->
@endsection
