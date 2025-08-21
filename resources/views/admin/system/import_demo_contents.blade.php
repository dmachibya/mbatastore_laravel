<div class="modal-dialog modal-sm">
  <div class="modal-content">
    {!! Form::open(['route' => 'admin.setting.system.importDemoContents', 'class' => 'ajax-form', 'data-toggle' => 'validator']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      {{ trans('app.form.form') }}
    </div>
    <div class="modal-body">
      @include('admin.system._confirmation')

      <p class="text-danger"><i class="fa fa-exclamation-triangle"></i> {!! trans('messages.import_demo_contents') !!}</p>

      @if (config('app.demo') == true)
        <div class="alert alert-warning"> {{ trans('messages.demo_restriction') }} </div>
      @endif
    </div>
    <div class="modal-footer">
      @unless (config('app.demo') == true)
        {!! Form::submit(trans('app.import_demo_contents'), ['class' => 'btn btn-flat btn-new confirm']) !!}
      @endunless
    </div>
    {!! Form::close() !!}
  </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->
