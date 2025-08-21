<!-- TikTok Pixel Base Code -->
@if (config('services.tiktok.pixel_id'))
  <script>
    ! function(w, d, t, u, n, a, m) {
      w['TikTokAnalyticsObject'] = n;
      w[n] = w[n] || function() {
        (w[n].q = w[n].q || []).push(arguments)
      }, w[n].l = 1 * new Date();
      a = d.createElement(t),
        m = d.getElementsByTagName(t)[0], a.async = 1, a.src = u, m.parentNode.insertBefore(a, m)
    }(window, document, 'script', 'https://analytics.tiktok.com/i18n/pixel/sdk.js', 'ttq');

    ttq('init', '{{ config('services.tiktok.pixel_id') }}');
    ttq('track', 'PageView');
  </script>
  <noscript><img src="https://analytics.tiktok.com/i18n/pixel/onelink.gif" /></noscript>
@endif
