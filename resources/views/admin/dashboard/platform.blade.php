@extends('admin.layouts.master')

@section('page-style')
  @include('plugins.ionic')
@endsection

@section('content')
  @include('admin.partials._check_misconfigured_subscription')

  <div class="row dashboard-total">
    <div class="col-md-3 stretch-card grid-margin">
      <div class="card bg-gradient-danger card-img-holder text-white">
        <div class="card-body">
          <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image">
          <h4 class="font-weight-normal mb-3">{{ trans('app.customers') }} <i class="icon ion-md-people float-right"></i>
          </h4>
          <h2 class="mb-5">{{ $customer_count }}</h2>
          <h6 class="card-text"><i class="icon ion-md-add"></i> {{ trans('app.new_in_30_days', ['new' => $new_customer_last_30_days, 'model' => trans('app.customers')]) }}</h6>
        </div>
      </div>
    </div>

    <div class="col-md-3 stretch-card grid-margin">
      <div class="card bg-gradient-info card-img-holder text-white">
        <div class="card-body">
          <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image">
          <h4 class="font-weight-normal mb-3">{{ trans('app.merchants') }} <i class="fa fa-bar-chart-o float-right"></i>
          </h4>
          <h2 class="mb-5">{{ $merchant_count }}</h2>
          <h6 class="card-text"><i class="icon ion-md-add"></i> {{ trans('app.new_in_30_days', ['new' => $new_merchant_last_30_days, 'model' => trans('app.merchants')]) }}</h6>
        </div>
      </div>
    </div>

    <div class="col-md-3 stretch-card grid-margin">
      <div class="card bg-gradient-primary card-img-holder text-white">
        <div class="card-body">
          <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image">
          <h4 class="font-weight-normal mb-3">{{ trans('app.orders') }} <i class="icon ion-md-cart float-right"></i>
          </h4>
          <h2 class="mb-5">{{ $total_order_count }}</h2>

          @php
            $difference = $todays_all_order_count - $yesterdays_all_order_count;
            $todays_order_percents = $todays_all_order_count == 0 ? 0 : round(($difference / $todays_all_order_count) * 100);
          @endphp

          @if ($todays_all_order_count == 0)
            <h6 class="card-text"><i class="icon ion-md-hourglass"></i> {{ trans('messages.no_orders', ['date' => trans('app.today')]) }}</h6>
          @else
            <h6 class="card-text"><i class="icon ion-md-arrow-{{ $difference < 0 ? 'down' : 'up' }}"></i> {{ trans('messages.todays_order_percents', ['percent' => $todays_order_percents, 'state' => $difference < 0 ? trans('app.down') : trans('app.up')]) }}</h6>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-3 stretch-card grid-margin">
      <div class="card bg-gradient-success card-img-holder text-white">
        <div class="card-body">
          <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image">
          <h4 class="font-weight-normal mb-3">{{ trans('app.todays_sale') }} <i class="icon ion-md-wallet float-right"></i>
          </h4>
          <h2 class="mb-5">
            {{ get_formated_currency($todays_sale_amount, 2, config('system_settings.currency.id')) }}
          </h2>

          @php
            $difference = $todays_sale_amount - $yesterdays_sale_amount;
            $todays_sale_percents = $todays_sale_amount == 0 ? 0 : round(($difference / $todays_sale_amount) * 100);
          @endphp

          @if ($todays_sale_amount == 0)
            <h6 class="card-text"><i class="icon ion-md-hourglass"></i> {{ trans('messages.no_sale', ['date' => trans('app.today')]) }}</h6>
          @else
            <h6 class="card-text"><i class="icon ion-md-arrow-{{ $difference < 0 ? 'down' : 'up' }}"></i> {{ trans('messages.todays_sale_percents', ['percent' => $todays_sale_percents, 'state' => $difference < 0 ? trans('app.down') : trans('app.up')]) }}</h6>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Info boxes -->
  <div class="row">
    <div class="col-md-8 col-sm-7 col-xs-12">
      <div class="row">
        <div class="col-sm-6 col-xs-12 nopadding-right">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="icon ion-md-filing"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ trans('app.pending_verifications') }}</span>
              <span class="info-box-number">
                {{ $pending_verifications }}
                <a href="{{ route('admin.vendor.shop.verifications') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}">
                  <i class="icon ion-md-paper-plane"></i>
                </a>
              </span>

              <div class="progress">
                <div class="progress-bar bg-warning" style="width: 0;"></div>
              </div>

              <span class="progress-description">
                <i class="icon ion-md-hourglass"></i>
                {{ trans_choice('messages.pending_verifications', $pending_verifications, ['count' => $pending_verifications]) }}
              </span>
            </div><!-- /.info-box-content -->
          </div>
        </div>

        <div class="col-sm-6 col-xs-12 px-2">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="icon ion-md-pulse"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ trans('app.pending_approvals') }}</span>
              <span class="info-box-number">
                {{ $pending_approvals }}
                <a href="{{ route('admin.vendor.shop.index') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}">
                  <i class="icon ion-md-paper-plane"></i>
                </a>
              </span>

              <div class="progress">
                <div class="progress-bar bg-info" style="width: 0;"></div>
              </div>

              <span class="progress-description">
                <i class="icon ion-md-hourglass"></i>
                {{ trans_choice('messages.pending_approvals', $pending_approvals, ['count' => $pending_approvals]) }}
              </span>
            </div><!-- /.info-box-content -->
          </div>
        </div>
      </div>
    </div><!-- /.col-*-* -->

    <div class="col-md-4 col-sm-5 col-xs-12 nopadding-left">
      <div class="info-box bg-red">
        <span class="info-box-icon"><i class="icon ion-md-megaphone"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">{{ trans('app.appealed_disputes') }}</span>
          <span class="info-box-number">
            {{ $dispute_count }}
            <a href="{{ route('admin.support.dispute.index') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}">
              <i class="icon ion-md-paper-plane"></i>
            </a>
          </span>

          @php
            $last_months = $last_60days_dispute_count - $last_30days_dispute_count;
            $difference = $last_30days_dispute_count - $last_months;
            $last_30_days_percents = $last_months == 0 ? 100 : round(($difference / $last_months) * 100);
          @endphp
          <div class="progress">
            <div class="progress-bar bg-danger" style="width: 0;"></div>
          </div>

          <span class="progress-description">
            <i class="icon ion-md-arrow-{{ $difference > 0 ? 'up' : 'down' }}"></i>
            {{ trans('messages.last_30_days_percents', ['percent' => $last_30_days_percents, 'state' => $difference > 0 ? trans('app.increase') : trans('app.decrease')]) }}
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
    </div>
    <!-- /.col-*-* -->
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs nav-justified">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-dollar"></i>
                {{ trans('app.sales_graph') }}</h3>
            </div>
          </ul> <!-- /.nav .nav-tabs -->

          <div class="tab-content total-sale-graph">
            <!-- Tab buttons for user interaction -->
            <div class="tab-container">
              <div class="tab-button active" data-timeframe="week">{{ trans('app.this_week') }}</div>
              <div class="tab-button" data-timeframe="month">{{ trans('app.this_month') }}</div>
              <div class="tab-button" data-timeframe="year">{{ trans('app.this_year') }}</div>
            </div>

            <!-- Chart canvas container -->
            <canvas id="saleChart" height="100vh"></canvas>
          </div> <!-- /.tab-content -->
        </div> <!-- /.nav-tabs-custom -->
      </div> <!-- /.box -->
    </div>
  </div>


  <div class="row">
    <div class="col-md-8">
      <div class="box">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs nav-justified">
            <div class="box-header with-border">
              <h3 class="box-title">
                <i class="icon ion-md-pulse hidden-sm"></i>
                {{ trans('app.visitors_graph') }}
            </div>
          </ul>
          <!-- /.nav .nav-tabs -->

          <div class="tab-content">
            <div class="tab-pane active" id="visitors_tab">
              <div>{!! $chart->container() !!}</div>
            </div>
          </div>
          <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div><!-- /.box -->
    </div>
    <div class="col-md-4">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            <i class="fa fa-pie-chart"></i>
            {{ trans('app.catalog_graph') }}
        </div>
        <div class="donutChart" style="min-height: 340px; padding: 30px 0;">
          <canvas id="productChart" class=""></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row dashboard-ticket-section">
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{ trans('app.open_tickets') }}
        </div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th width="65%">{{ trans('app.subject') }}</th>
              <th>{{ trans('app.priority') }}</th>
              <th><i class="icon ion-md-chatbubbles"></i></th>
              <th>{{ trans('app.updated_at') }}</th>
            </tr>
          </thead>
          <tbody class="box-body">
            @forelse($open_tickets->take(5) as $ticket)
              <tr>
                <td>
                  <span class="label label-outline"> {{ $ticket->category->name }} </span>
                  <p class="indent5">
                    <a href="{{ route('admin.support.ticket.show', $ticket->id) }}">{{ $ticket->subject }}</a>
                  </p>
                </td>
                <td>{!! $ticket->priorityLevel() !!}</td>
                <td><span class="label label-default">{{ $ticket->replies_count }}</span></td>
                <td>{{ $ticket->updated_at->diffForHumans() }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3">{{ trans('app.no_data_found') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{ trans('app.top_customers') }}
        </div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.name') }}</th>
              <th><i class="icon ion-md-cart"></i></th>
              <th>{{ trans('app.revenue') }}</th>
            </tr>
          </thead>

          <tbody class="box-body">
            @forelse($top_customers as $customer)
              <tr>
                <td>
                  @if ($customer->image)
                    <img src="{{ get_storage_file_url(optional($customer->image)->path, 'tiny') }}" class="img-circle" alt="{{ trans('app.avatar') }}">
                  @else
                    <img src="{{ get_gravatar_url($customer->email, 'tiny') }}" class="img-circle" alt="{{ trans('app.avatar') }}">
                  @endif

                  <p class="indent5">
                    @can('view', $customer)
                      <a href="javascript:void(0)" data-link="{{ route('admin.admin.customer.show', $customer->id) }}" class="ajax-modal-btn modal-btn">
                        {{ $customer->getName() }}
                      </a>
                    @else
                      {{ $customer->getName() }}
                    @endcan
                  </p>
                </td>

                <td>
                  <span class="label label-outline">{{ $customer->orders_count }}</span>
                </td>

                <td>
                  {{ get_formated_currency(round($customer->orders->sum('total')), 2, config('system_settings.currency.id')) }}
                </td>
              </tr>
            @empty

              <tr>
                <td colspan="3">{{ trans('app.no_data_found') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="col-md-3">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{ trans('app.top_vendors') }}
        </div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.name') }}</th>
              <th><i class="icon ion-md-cart"></i></th>
              <th>{{ trans('app.revenue') }}</th>
            </tr>
          </thead>
          <tbody class="box-body">
            @forelse($top_vendors as $vendor)
              <tr>
                <td>
                  <img src="{{ get_storage_file_url(optional($vendor->logoImage)->path, 'tiny') }}" class="img-circle" alt="{{ trans('app.logo') }}">
                  <p class="indent5">
                    @can('view', $vendor)
                      <a href="javascript:void(0)" data-link="{{ route('admin.vendor.shop.show', $vendor->id) }}" class="ajax-modal-btn modal-btn">{{ $vendor->name }}</a>
                    @else
                      {{ $vendor->name }}
                    @endcan
                  </p>
                </td>
                <td>
                  <span class="label label-outline">{{ $vendor->inventories_count }}</span>
                </td>
                <td>
                  {{ $vendor->lifetime_value }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3">{{ trans('app.no_data_found') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="row dashboard-product-section">
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{ trans('app.top_sale_products') }}
        </div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.image') }}</th>
              <th>{{ trans('app.name') }}</th>
              <th>{{ trans('app.sold') }}</th>
              <th>{{ trans('app.gtin') }}</th>
              <th>{{ trans('app.action') }}</th>
            </tr>
          </thead>
          <tbody class="box-body">
            @foreach ($top_selling_products as $product)
              <tr>
                <td><img src="{{ get_storage_file_url(optional($product->featureImage)->path, 'tiny') }}" alt="{{ $product->name }}" class="img-thumbnail"></td>
                <td><a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.show', $product->id) }}" class="ajax-modal-btn">
                    {{ $product->name }}
                  </a></td>
                <td><span class="label label-outline">{{ $product->inventories_sum_sold_quantity }}</span></td>
                <td><span class="label label-outline">{{ $product->gtin_type }}</span> {{ $product->gtin }}</td>
                <td>
                  @can('update', $product)
                    <a class="btn btn-primary" href="{{ route('admin.catalog.product.edit', $product->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>
                  @endcan
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{ trans('app.recently_added_products') }}
        </div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.image') }}</th>
              <th>{{ trans('app.name') }}</th>
              <th>{{ trans('app.sold') }}</th>
              <th>{{ trans('app.gtin') }}</th>
              <th>{{ trans('app.action') }}</th>
            </tr>
          </thead>
          <tbody class="box-body">
            @foreach ($latest_products as $product)
              <tr>
                <td><img src="{{ get_storage_file_url(optional($product->featureImage)->path, 'tiny') }}" alt="{{ $product->name }}" class="img-thumbnail"></td>
                <td><a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.show', $product->id) }}" class="ajax-modal-btn">
                    {{ $product->name }}
                  </a></td>
                <td><span class="label label-outline">{{ $product->inventories_sum_sold_quantity }}</span></td>
                <td><span class="label label-outline">{{ $product->gtin_type }}</span> {{ $product->gtin }}</td>
                <td>
                  @can('update', $product)
                    <a class="btn btn-primary" href="{{ route('admin.catalog.product.edit', $product->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>
                  @endcan
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="row dashboard-product-section">
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{ trans('app.top_sale_brands') }}
        </div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.image') }}</th>
              <th>{{ trans('app.name') }}</th>
              <th>{{ trans('app.sold') }}</th>
              <th>{{ trans('app.country') }}</th>
              <th>{{ trans('app.action') }}</th>
            </tr>
          </thead>
          <tbody class="box-body">
            @foreach ($top_selling_brands as $manufacturer)
              <tr>
                <td><img src="{{ get_logo_url($manufacturer, 'tiny') }}" class="img-sm" alt="{{ trans('app.image') }}"></td>
                <td><a href="#" class="ajax-modal-btn">
                    {{ $manufacturer->name }}
                  </a></td>
                <td><span class="label label-outline">{{ $manufacturer->inventories_sum_sold_quantity }}</span></td>
                <td>{{ optional($manufacturer->country)->name }}</td>
                <td>
                  @can('update', $manufacturer)
                    <a href="javascript:void(0)" data-link="{{ route('admin.catalog.manufacturer.edit', $manufacturer->id) }}" class="ajax-modal-btn btn btn-primary"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
                  @endcan
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            {{ trans('app.top_sale_categories') }}
        </div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.image') }}</th>
              <th>{{ trans('app.name') }}</th>
              <th>{{ trans('app.sold') }}</th>
              <th>{{ trans('app.parent') }}</th>
              <th>{{ trans('app.action') }}</th>
            </tr>
          </thead>
          <tbody class="box-body">
            @foreach ($top_selling_categories as $category)
              <tr>
                <td><img src="{{ get_storage_file_url(optional($category->featureImage)->path, 'tiny') }}" alt="{{ $category->name }}" class="img-thumbnail"></td>
                <td><a href="#" class="ajax-modal-btn">
                    {{ $category->name }}
                  </a></td>
                <td><span class="label label-outline">{{ $category->listings_sum_sold_quantity }}</span></td>
                <td>{{ $category->subGroup->name }} <i class="fa fa-angle-double-right small"></i> {{ $category->subGroup->group->name }}</td>
                <td>
                  @can('update', $category)
                    <a href="javascript:void(0)" data-link="{{ route('admin.catalog.category.edit', $category->id) }}" class="ajax-modal-btn btn btn-primary"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
                  @endcan
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@section('page-script')
  @include('plugins.chart')

  {!! $chart->script() !!}

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.2/Chart.min.js"></script>
  <script>
    var ctx = document.getElementById("productChart").getContext('2d');
    var productChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ["{{ trans('app.admin') }}", "{{ trans('app.merchant') }}", "{{ trans('app.total') }}"],
        datasets: [{
          backgroundColor: [
            "#f39c12",
            "#ef486a",
            "#0abb75"
          ],
          data: [<?= $admin_created_total_product_count ?>, <?= $merchant_created_total_product_count ?>, <?= $admin_created_total_product_count + $merchant_created_total_product_count ?>]
        }]
      }
    });
  </script>

  <script>
    // Sample data for last week, last month, and last year
    const dataThisWeek = <?= $total_weekly_sale_report ?>;
    const dataThisMonth = <?= $total_monthly_sale_report ?>;
    const dataThisYear = <?= $total_yearly_sale_report ?>;

    // Initialize the chart with data for the last week
    const saleCtx = document.getElementById('saleChart').getContext('2d');
    const saleChart = new Chart(saleCtx, {
      type: 'line',
      data: {
        labels: <?= \App\Helpers\ChartHelper::getSaleAmount('week') ?>, // Adjust labels as needed
        datasets: [{
          label: "@lang('app.this_week')",
          data: dataThisWeek,
          backgroundColor: '#CAD8F8',
          borderColor: '#225DE4',
          borderWidth: 2,
          pointRadius: 4,
        }]
      },
      options: {
        // Chart options here
      }
    });

    // Function to update the chart with new data based on the selected time frame
    function updateChart(timeframe, data, color) {
      saleChart.data.datasets[0].label = `This ${timeframe.charAt(0).toUpperCase() + timeframe.slice(1)}`;
      saleChart.data.datasets[0].data = data;
      saleChart.data.datasets[0].borderColor = "#225DE4";
      saleChart.data.datasets[0].backgroundColor = color;
      saleChart.data.datasets[0].pointRadius = 4;

      saleChart.data.labels = getLabels(timeframe); // Update labels based on the selected time frame
      saleChart.update();
    }

    // Helper function to get labels based on the selected time frame
    function getLabels(timeframe) {
      switch (timeframe) {
        case 'week':
          return <?= \App\Helpers\ChartHelper::getSaleAmount('week') ?>;
        case 'month':
          return <?= \App\Helpers\ChartHelper::getSaleAmount('month') ?>;
        case 'year':
          return <?= \App\Helpers\ChartHelper::getSaleAmount('year') ?>;
        default:
          return [];
      }
    }

    // Add event listeners to tab buttons for user interaction
    document.querySelectorAll('.tab-button').forEach(tabButton => {
      tabButton.addEventListener('click', () => {
        const timeframe = tabButton.getAttribute('data-timeframe');
        const data = getDataForTimeframe(timeframe); // Replace with your data retrieval logic
        const color = getColorForTimeframe(timeframe); // Adjust color as needed
        updateChart(timeframe, data, color);

        // Toggle the 'active' class among tabs
        document.querySelectorAll('.tab-button').forEach(tab => {
          tab.classList.remove('active');
        });
        tabButton.classList.add('active');
      });
    });

    // Function to retrieve data based on the selected time frame
    function getDataForTimeframe(timeframe) {
      switch (timeframe) {
        case 'week':
          return dataThisWeek;
        case 'month':
          return dataThisMonth;
        case 'year':
          return dataThisYear;
        default:
          return [];
      }
    }

    // Function to get a color based on the selected time frame
    function getColorForTimeframe(timeframe) {
      switch (timeframe) {
        case 'week':
          return '#CAD8F8';
        case 'month':
          return '#CAD8F8';
        case 'year':
          return '#CAD8F8';
        default:
          return '';
      }
    }
  </script>
@endsection
