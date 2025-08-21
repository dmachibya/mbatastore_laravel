{{ $customer->name }}

@if(\App\Models\SystemConfig::CustomerNeedsApproval())
    @if($customer->approval_status === true)
        <div class="badge badge-pill bg-green ml-2"> {{ trans('app.statuses.approved') }} </div>
    @elseif ($customer->approval_status === false)
        <div class="badge badge-pill bg-red ml-2"> {{ trans('app.statuses.declined') }} </div>
    @else
        <div class="badge badge-pill ml-2"> {{ trans('app.statuses.pending') }} </div>
    @endif
@endif
