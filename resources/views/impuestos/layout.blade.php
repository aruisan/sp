<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>IMPUESTOS - Providencia Islas</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{asset('/assets/adminLTE/bootstrap/css/bootstrap.min.css') }}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{asset('/assets/adminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
    <!-- Theme style -->

    <link rel="stylesheet" href="{{asset('/assets/adminLTE/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{asset('/assets/adminLTE/css/skins/_all-skins.min.css') }}">

    <link rel="shortcut icon" href="{{ asset('/img/logoSiex.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('/img/logoSiex.png') }}" type="image/x-icon">

    <!-- DataTables CSS -->

    <link href=" https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link  rel="stylesheet"  href="{{asset('/assets/sb-admin/css/sb-admin-2.css')}}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('/assets/adminLTE/plugins/datatables/dataTables.bootstrap.css') }}">
    <!-- Morris Charts CSS -->
    <link href="{{asset('/assets/morrisjs/morris.css')}}" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="{{asset('/assets/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"    rel="stylesheet">

    <!--alertas con toast-->
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Mi Estilo -->
    <link href="{{asset('/css/miStilo.css')}}" rel="stylesheet" type="text/css">

    <!-- Select 2 -->
    <link href="{{ asset('/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/v/bs-3.3.7/jszip-3.1.3/pdfmake-0.1.27/dt-1.10.15/b-1.3.1/b-html5-1.3.1/b-print-1.3.1/datatables.min.css" rel="stylesheet"/>
    <link href="{{asset('/assets/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{asset('/assets/datatables-responsive/dataTables.responsive.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('/assets/adminLTE/style.css') }}">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/impuestos') }}">SIEX - Portal de Impuestos</a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">&nbsp; </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @guest
                        <li><a href="{{ route('login') }}">Acceder a la cuenta</a></li>
                        <li><a href="{{ route('register') }}">Registro para pago de impuestos</a></li>
                    @else
                        <li class="dropdown">
                            <a class="dropdown-toggle item-menu" href="/user">
                                {{ Auth::user()->name }}
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle item-menu" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Cerrar Sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    @yield('container')
</div>

<!-- Scripts -->
<!-- jQuery 2.1.4 -->
<script src="{{asset('/assets/adminLTE/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<script src="{{asset('js/myJs.js')}}"></script>

<script src="{{asset('/assets/adminLTE/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>

<!-- Bootstrap 3.3.5 -->
{{-- <script src="{{asset('/assets/adminLTE/bootstrap/js/bootstrap.min.js')}}"></script> --}}
<script src="{{asset('/assets/adminLTE/script.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('/assets/adminLTE/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('/assets/adminLTE/js/app.min.js')}}"></script>
<!-- Select 2-->

<script src="{{ asset('js/lib/select2/select2.min.js') }}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{asset('/assets/adminLTE/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{asset('/assets/bootstrap/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.3.2/js/mdb.min.js"></script>
<!--vue-->
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<!--toast-->
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@include('layouts/cuerpo/toastRequestImpuestos')
<!--data tables-->
<script src="{{ asset('/assets/adminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/assets/adminLTE/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/lib/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js') }}"></script>
<script src="{{ asset('js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js') }}"></script>
<!-- DataTables JavaScript -->
<script src="{{asset('/assets/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/assets/datatables-plugins/dataTables.bootstrap.min.js')}}"></script>

@yield('scripts')

<script>
    $(document).keydown(function(event){
        if(event.keyCode==123){
            return false;
        }
        else if ((event.ctrlKey && event.shiftKey && event.keyCode==73) || (event.ctrlKey && event.shiftKey && event.keyCode == 74)) {
            return false;
        }
    });

    $(document).on("contextmenu",function(e){
        e.preventDefault();
    });
</script>
</body>
</html>