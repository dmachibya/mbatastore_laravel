<div class="alert alert-warning">
  <strong>
    <i class="icon fas fa-info-circle no-fill"></i> {{ trans('app.notice') }}
  </strong>

  {{ trans('messages.update_from_merchant_notice') }}
</div>

<div class="text-center mb-4">
  <a href="{{ route('customer.switchToMerchant') }}" class="btn btn-primary btn-lg">
    <i class="fal fa-dashboard"></i> {{ trans('theme.view_merchant_dashboard') }}
  </a>
</div>
