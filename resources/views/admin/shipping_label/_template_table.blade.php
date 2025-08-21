<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">{{ trans('app.shipping_label_templates') }}</h3>
    <div class="box-tools">
      <a href="javascript:void(0)" data-link="{{ route('admin.shipping.label.create') }}" class="ajax-modal-btn btn btn-new btn-flat">{{ trans('app.upload_template') }}</a>
    </div>
  </div> <!-- /.box-header -->
  <div class="box-body">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>{{ trans('app.no') }}</th>
          <th>{{ trans('app.name') }}</th>
          <th>{{ trans('app.options') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($shipping_label_templates as $template)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
              {{ $template->name }} &nbsp;
              @if ($template->is_default)
                <span class="label label-info">{{ trans('app.default') }}</span>
              @endif
            </td>
            <td>
                <a href="{{ route('admin.shipping.label.show', $template) }}" data-toggle="tooltip" title="{{ trans('app.view_template') }}"><i class="fa fa-eye"></i></a>&nbsp;
                <a href="{{ route('admin.shipping.label.download', $template) }}" data-toggle="tooltip" title="{{ trans('app.download_template') }}"><i class="fa fa-download"></i></a>&nbsp;
                
                @unless ($template->is_default||($template->is_from_platform && auth()->user()->isMerchant()))
                  <a href="{{ route('admin.shipping.label.delete',$template) }}" data-toggle="tooltip" title="{{ trans('app.delete') }}" class="confirm ajax-silent"><i class="fa fa-trash"></i></a>&nbsp;                
                @endunless
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>  
  </div>
</div>