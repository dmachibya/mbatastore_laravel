@isset($sliders)
  <div id="shop-page-slider">
    @desktop
      @foreach ($sliders as $slider)
        <img class="lazy" src="{{ get_storage_file_url($slider['feature_image']['path'] ?? null, 'cover_thumb') }}" data-src="{{ get_storage_file_url($slider['feature_image']['path'] ?? null, 'full') }}" alt="{{ $slider['title'] ?? trans('theme.slider_image') . ' ' . $loop->count }}">
      @endforeach
    @elsedesktop
      @foreach ($sliders as $slider)
        <img class="lazy" src="{{ get_storage_file_url($slider['mobile_image']['path'] ?? null, 'cover_thumb') }}" data-src="{{ get_storage_file_url($slider['mobile_image']['path'] ?? null, 'full') }}" alt="{{ $slider['title'] ?? trans('theme.slider_image') . ' ' . $loop->count }}">
      @endforeach
    @enddesktop
  </div>
@endisset
