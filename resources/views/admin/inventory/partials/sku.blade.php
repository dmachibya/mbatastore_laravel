@can('view', $inventory)
  @if ($inventory->variants_count)
    <a href="javascript:void(0)" data-link="{{ route('admin.stock.inventory.show', $inventory->id) }}" class="ajax-modal-btn">
      <span class="label label-default">
        {{ $inventory->variants_count + 1 . ' ' . trans('app.skus') }}
      </span>
    </a>
  @else
    {{ $inventory->sku }}
  @endif
@endcan
