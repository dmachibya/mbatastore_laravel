@extends('admin.layouts.master')

@php
  $translation_language = app()->getLocale();
@endphp

@section('content')
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">{{ trans('app.category') . ' : ' }}{{ $categoryGroup->name . ' | ' }}{{ trans('app.category_sub_groups') }}</h3>
    </div> <!-- /.box-header -->
    <div class="box-body responsive-table">
      <table class="table table-hover table-2nd-no-sort">
        <thead>
          <tr>
            @can('massDelete', \App\Models\CategorySubGroup::class)
              <th class="massActionWrapper">
                <!-- Check all button -->
                <div class="btn-group ">
                  <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                    <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top" title="{{ trans('app.select_all') }}"></i>
                  </button>
                  <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="javascript:void(0)" data-link="{{ route('admin.catalog.categorySubGroup.massTrash') }}" class="massAction " data-doafter="reload"><i class="fa fa-trash"></i> {{ trans('app.trash') }}</a></li>
                    <li><a href="javascript:void(0)" data-link="{{ route('admin.catalog.categorySubGroup.massDestroy') }}" class="massAction " data-doafter="reload"><i class="fa fa-times"></i> {{ trans('app.delete_permanently') }}</a></li>
                  </ul>
                </div>
              </th>
            @endcan
            <th>{{ trans('app.cover_image') }}</th>
            <th>{{ trans('app.category_sub_group') }}</th>
            <th>{{ trans('app.order') }}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody id="massSelectArea">
          @foreach ($categorySubGrps as $categorySubGrp)
            <tr>
              @can('massDelete', \App\Models\CategorySubGroup::class)
                <td><input id="{{ $categorySubGrp->id }}" type="checkbox" class="massCheck"></td>
              @endcan
              <td>
                <img src="{{ get_storage_file_url(optional($categorySubGrp->coverImage)->path, 'cover_thumb') }}" class="img-sm" alt="{{ trans('app.cover_image') }}">
              </td>
              <td> {{ $categorySubGrp->name }}
                @unless ($categorySubGrp->active)
                  <span class="label label-default indent5 small">{{ trans('app.inactive') }}</span>
                @endunless
              </td>
              <td>{{ $categorySubGrp->order }}</td>
              <td class="row-options">
                @can('update', $categorySubGrp)
                  <a href="javascript:void(0)" data-link="{{ route('admin.catalog.categorySubGroup.edit', $categorySubGrp->id) }}" class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="Edit" class="fa fa-edit"></i></a>&nbsp;
                  <a href="{{ route('admin.catalog.categorySubGroup.translate.form', ['categorySubGroup' => $categorySubGrp, 'language' => $translation_language]) }}"><em class="fa fa-language" data-toggle="tooltip" data-placement="top" title="{{ trans('app.manage_translations') }}"></em></a>&nbsp;
                @endcan

                @can('delete', $categorySubGrp)
                  {!! Form::open(['route' => ['admin.catalog.categorySubGroup.trash', $categorySubGrp->id], 'method' => 'delete', 'class' => 'data-form']) !!}
                  {!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'confirm ajax-silent', 'title' => 'Trash', 'data-toggle' => 'tooltip', 'data-placement' => 'top']) !!}
                  {!! Form::close() !!}
                @endcan
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->

  <div class="box collapsed-box">
    <div class="box-header with-border">
      <h3 class="box-title">
        @can('massDelete', \App\Models\CategorySubGroup::class)
          {!! Form::open(['route' => ['admin.catalog.categorySubGroup.emptyTrash'], 'method' => 'delete', 'class' => 'data-form']) !!}
          {!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'confirm btn btn-default btn-flat ajax-silent', 'title' => trans('help.empty_trash'), 'data-toggle' => 'tooltip', 'data-placement' => 'right']) !!}
          {!! Form::close() !!}
        @else
          <i class="fa fa-trash-o"></i>
        @endcan
        {{ trans('app.trash') }}
      </h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div> <!-- /.box-header -->
    <div class="box-body responsive-table">
      <table class="table table-hover table-option">
        <thead>
          <tr>
            <th>{{ trans('app.category_sub_group') }}</th>
            <th>{{ trans('app.parent') }}</th>
            <th>{{ trans('app.deleted_at') }}</th>
            <th>{{ trans('app.option') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($trashes as $trash)
            <tr>
              <td>{{ $trash->name }}</td>
              <td>
                @if ($trash->group->deleted_at)
                  <i class="fa fa-trash-o small"></i>
                @endif
                {{ $trash->group->name }}
              </td>
              <td>{{ $trash->deleted_at->diffForHumans() }}</td>
              <td class="row-options">
                @can('delete', $trash)
                  <a href="{{ route('admin.catalog.categorySubGroup.restore', $trash->id) }}"><i data-toggle="tooltip" data-placement="top" title="Restore" class="fa fa-database"></i></a>&nbsp;

                  {!! Form::open(['route' => ['admin.catalog.categorySubGroup.destroy', $trash->id], 'method' => 'delete', 'class' => 'data-form']) !!}
                  {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'confirm ajax-silent', 'title' => 'Delete Permanently', 'data-toggle' => 'tooltip', 'data-placement' => 'top']) !!}
                  {!! Form::close() !!}
                @endcan
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->
@endsection
