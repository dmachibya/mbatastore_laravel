@extends('admin.layouts.master')

@section('content')
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">{{ trans('app.pdf_templates') }}</h3>
      <div class="box-tools">
        <a href="javascript:void(0)" data-link="{{ route('admin.utility.pdfTemplate.create') }}" class="ajax-modal-btn btn btn-new btn-flat">{{ trans('app.upload_template') }}</a>
      </div>
    </div> <!-- /.box-header -->

    <div class="box-body">
      <table class="table table-hover table-2nd-no-sort">
        <thead>
          <tr>
            <th>{{ trans('app.name') }}</th>
            <th>{{ trans('app.type') }}</th>
            <th>{{ trans('app.updated_at') }}</th>
            <th>{{ trans('app.active') }}</th>
            <th>{{ trans('app.options') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($templates as $template)
            <tr>
              <td>{{ $template->name }}
                @if ($template->is_default)
                  &nbsp;<span class="label label-info">{{ trans('app.default') }}</span>
                @endif
              </td>
              <td>{{ $template->type }}</td>
              <td>{{ $template->updated_at->toFormattedDateString() }}</td>
              <td>
                <span class="badge {{ $template->active ? 'bg-green' : 'bg-grey' }}">
                  {{ $template->active ? trans('app.active') : trans('app.inactive') }}
                </span>
              </td>
              <td>
                <a href="{{ route('admin.utility.pdfTemplate.show', $template) }}" data-toggle="tooltip" title="{{ trans('app.view_template') }}" class="text-muted"><i class="fa fa-eye"></i></a>&nbsp;

                <a href="{{ route('admin.utility.pdfTemplate.download', $template) }}" data-toggle="tooltip" title="{{ trans('app.download_template') }}" class="text-muted"><i class="fa fa-download"></i></a>&nbsp;

                <a href="javascript:void(0)" data-link="{{ route('admin.utility.pdfTemplate.edit', $template) }}" class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;

                @if ($template->is_default)
                  <i class="fa fa-bell-o text-muted" data-toggle="tooltip" data-placement="left" title="{{ trans('messages.freezed_model') }}"></i>
                @else
                  {!! Form::open(['route' => ['admin.utility.pdfTemplate.destroy', $template], 'method' => 'delete', 'class' => 'data-form']) !!}
                  {!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'confirm ajax-silent', 'title' => trans('app.delete'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) !!}
                  {!! Form::close() !!}
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
