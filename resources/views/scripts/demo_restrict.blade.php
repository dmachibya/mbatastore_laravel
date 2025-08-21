@if (config('app.demo') == true)
  <script type="text/javascript">
    $(".demo-restrict").off().on('click', function(e) {
      e.preventDefault();
      $('.login-box').prepend("<p class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>{!! trans('messages.demo_restriction') !!}</p>");
    });
  </script>
@endif
