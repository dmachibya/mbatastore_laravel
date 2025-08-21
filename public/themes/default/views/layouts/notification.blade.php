$(document).ready(function(){
toastr.options = {
"closeButton": true,
"debug": false,
"newestOnTop": false,
"progressBar": false,
"positionClass": "toast-bottom-center",
"preventDuplicates": false,
"onclick": null,
"showEasing": "swing",
"hideEasing": "linear",
"showMethod": "fadeIn",
"hideMethod": "fadeOut"
};
toastr.{{ $type ?? 'info' }}('{{$message}}');
{{--  $.notify({--}}
{{--    // Oprions--}}
{{--    icon: 'fas fa-{{ $icon ?? 'paw' }}',--}}
{{--    title: "<strong>{{ trans('theme.' . $type) }}:</strong> ",--}}
{{--    message: '{{ $message ?? '' }}'--}}
{{--  },{--}}
{{--    // Settings--}}
{{--    type: '{{ $type ?? 'info' }}',--}}
{{--    delay: 400,--}}
{{--    placement: {--}}
{{--      from: "bottom",--}}
{{--      align: "center"--}}
{{--    }--}}
{{--  });--}}
});
