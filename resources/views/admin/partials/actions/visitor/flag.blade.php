@if (file_exists(sys_image_path('flags') . $visitor->country_code . '.png'))
  <img src="{{ asset(sys_image_path('flags') . $visitor->country_code . '.png') }}" class="lang-flag" data-toggle="tooltip" data-placement="right" title="{{ $visitor->country_code }}">
@else
  {{ $visitor->country_code }}
@endif
