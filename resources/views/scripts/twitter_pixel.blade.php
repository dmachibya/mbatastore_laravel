<!-- Twitter Pixel Base Code -->
@if (config('services.twitter.pixel_id'))
  <script async src="https://static.ads-twitter.com/uwt.js"></script>
  <script>
    ! function(e, t, n, s, u, a) {
      e.twq ||
        ((s = e.twq = function() {
            s.exe ? s.exe.apply(s, arguments) : s.queue.push(arguments);
          }),
          (s.version = "1.1"),
          (s.queue = []),
          (u = t.createElement(n)),
          (u.async = !0),
          (u.src = "//static.ads-twitter.com/uwt.js"),
          (a = t.getElementsByTagName(n)[0]),
          a.parentNode.insertBefore(u, a))
    }(window, document, "script");
    // Insert your Pixel ID here
    twq("init", '{{ config('services.twitter.pixel_id') }}');
    twq("track", "PageView");
  </script>
@endif
