<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": 3000,
            "extendedTimeOut": 0,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": true
        }
		@if(Session::has('error'))
            toastr.error('{{ Session::get('error')}}');
        @elseif(Session::has('success'))
            toastr.success('{{ Session::get('success') }}');
        @elseif(Session::has('warning'))
            toastr.warning('{{ Session::get('warning') }}');
        @endif
    });
</script>

