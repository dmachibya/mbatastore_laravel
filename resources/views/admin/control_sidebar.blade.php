<aside class="control-sidebar control-sidebar-dark">
  <!-- Create the tabs -->
  <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
    <li class="active">
      <a href="javascript:void(0)" data-toggle="tab">
        <i class="fa fa-gears"></i> {{ trans('app.control_panel') }}
      </a>
    </li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
    <!-- Home tab content -->
    <div class="tab-pane active" id="control-sidebar-home-tab">

      {{-- License Settings --}}
      <h3 class="control-sidebar-heading"><i class="fa fa-key"></i> {{ trans('app.license') }}</h3>
      <a href="" class="btn btn-warning btn-block">{{ trans('app.license_reset') }}</a>

      <h3 class="control-sidebar-heading"><i class="fa fa-key"></i> {{ trans('app.system') }}</h3>
      <a href="{{ route('admin.incevio.clear') }}" class="btn btn-default btn-block btn-lg confirm">
        <i class="fa fa-info-circle" data-toggle="tooltip" title="{!! trans('help.help_clear_cache') !!}" data-placement="left"></i>
        {{ trans('app.clear_cache') }}
      </a>

      <button type="button" class="btn btn-block btn-info btn-sm">Primary</button>

      <button type="button" class="btn btn-block btn-info btn-sm">Primary</button>
      <button type="button" class="btn btn-block btn-new">Secondary</button>
      <button type="button" class="btn btn-block btn-success">Warning</button>

      <h3 class="control-sidebar-heading">Recent Activity</h3>
      <ul class="control-sidebar-menu">
        <li>
          <a href="javascript::;">
            <i class="menu-icon fa fa-birthday-cake bg-red"></i>
            <div class="menu-info">
              <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
              <p>Will be 23 on April 24th</p>
            </div>
          </a>
        </li>
      </ul>
      <!-- /.control-sidebar-menu -->

      <h3 class="control-sidebar-heading">Tasks Progress</h3>
      <ul class="control-sidebar-menu">
        <li>
          <a href="javascript::;">
            <h4 class="control-sidebar-subheading">
              Custom Template Design
              <span class="label label-danger pull-right">70%</span>
            </h4>

            <div class="progress progress-xxs">
              <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
            </div>
          </a>
        </li>
      </ul>
      <!-- /.control-sidebar-menu -->

    </div>
    <!-- /.tab-pane -->
    <!-- Stats tab content -->
    <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
    <!-- /.tab-pane -->
    <!-- Settings tab content -->
  </div>
</aside>
