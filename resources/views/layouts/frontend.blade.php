<!DOCTYPE html>
<html lang="es">
    <head>

        <title> SIEX - PROVIDENCIA Y SANTA CATALINA ISLAS </title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">



        <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.css') }}">
        <link href="{{asset('/assets/datatables-plugins/dataTables.bootstrap.css')}}" rel="stylesheet">
        <link href=" https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css" rel="stylesheet">
        <link href="{{asset('/assets/datatables-responsive/dataTables.responsive.css')}}" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('/assets/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/css/footer.css') }}">

        <link rel="shortcut icon" href="{{ asset('/img/logoSiex.png') }}" type="image/x-icon">
        <link rel="icon" href="{{ asset('/img/logoSiex.png') }}" type="image/x-icon">

        <style type="text/css">
            .nav_1{
                background-color: #AACD29;
                height: 30px !important;
            }

            .nav_2{
                background-color: #004080;
                height: 60px !important;
            }

            .navbar-default .navbar-nav > li > a {
                color: white;
            }

            nav{
                margin: 0 !important;
            }

            .li-nav_2 > a:hover{
                color: white !important;
            }

            .li-nav_2:hover{
              background: rgba(0,0,0,.4) !important;
            }

            .navbar-default{
                border: 0px !important;
            }

            .navbar{
                border-radius: 0px !important;
            }

            .sin-padding{
                padding: 0px;
            }
            .margin-horizontal-20{
                margin-left: 20px !important;
                margin-right: 20px;
            }

            .redes-sociales > li{
                border-radius: 50%;
                margin: 0px 3px;
                color: white;
                background-color: #354d61;
            }

            .redes-sociales > li:hover{
                background-color: white;
            }

            .redes-sociales{
                padding: 2px;
            }

            .sombra{
                -webkit-box-shadow: 0px 10px 9px -1px rgba(156,150,156,1);
-moz-box-shadow: 0px 10px 9px -1px rgba(156,150,156,1);
box-shadow: 0px 10px 9px -1px rgba(156,150,156,1);
            }
        </style>
        @yield('css')
    <head>
    <body>
        <div class="sombra">
            <nav class="navbar navbar-default nav_1">
              <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand" href="#">
                      <img src="{{asset('img/principal/logo_gov.png')}}">
                  </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">Politicas</a></li>

                    <li>
                      @if(Auth::guest())
                        <a data-toggle="modal" data-target="#modal-ingresar"><b>Entrar</b></a></li>
                      @else
                        <a href="{{url('/dashboard')}}"><b>Plataforma</b></a></li>
                      @endif
                    <li>
                        <input type="text" class="form-control" placeholder="Buscar" style="margin-top: 7px; border-radius:20px;">
                    </li>
                  </ul>
                </div><!-- /.navbar-collapse -->
              </div><!-- /.container-fluid -->
            </nav>

            <nav class="navbar navbar-default nav_2">
              <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand sin-padding margin-horizontal-20" href="#">
                    <img src="{{asset('img/principal/islas.png')}}" width="50">
                  </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav">
                    <li class="li-nav_2"><a href="/">INICIO</a></li>
                    <li class="dropdown li-nav_2">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">TRAMITES <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="#">Chat</a></li>
                        <li><a href="#">Contactos</a></li>
                        <li><a href="#">Peticiones QRS</a></li>
                        <li><a href="#">Agende su cita</a></li>
                        <li><a href="#">Normatividad</a></li>
                        <li><a href="#">Dependencias</a></li>
                        <li><a href="#">Avisos y Citaciones</a></li>
                        <li><a href="#">Solicitud de Licencias Urbanas</a></li>
                        <li><a href="#">Notificacion Judicial</a></li>
                      </ul>
                    </li>
                    <li class="dropdown li-nav_2">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">PRENSA <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="#">Revista Digital</a></li>
                        <li><a href="#">Boletines y Noticias</a></li>
                        <li><a href="#">Eventos</a></li>
                        <li><a href="#">Emisora en vivo</a></li>
                        <li><a href="#">Galeria de fotos</a></li>
                        <li><a href="#">Videos islas</a></li>
                      </ul>
                    </li>
                    <li class="dropdown li-nav_2">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">IMPUESTOS <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li style="cursor: pointer"><a data-toggle="modal" data-target="#modal-ingresar">Contribuyente Registrado</a></li>
                        <li><a href="{{url('/register')}}">Nuevo Contribuyente</a></li>
                        <li><a href="#">Información</a></li>
                      </ul>
                    </li>
                    <li>
                      <a href="{{route('estadistica.public')}}">
                        ESTADISTICA
                      </a>
                    </li>
                    <li class="dropdown li-nav_2">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">TRANSPARENCIA <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="#">Rendición de cuentas</a></li>
                        <li><a href="#">Informes de empalme</a></li>
                        <li><a href="#">Bienes inmuebles del municipio</a></li>
                        <li><a href="#">Plan anticorrupción</a></li>
                        <li><a href="#">Codigo de integridad</a></li>
                        <li><a href="#">Politicas publicas y MIPG</a></li>
                        <li><a href="#">Plan de compras</a></li>
                        <li><a href="#">Presupuesto</a></li>
                        <li><a href="#">Plan de acción</a></li>
                      </ul>
                    </li>
                  </ul>
                </div><!-- /.navbar-collapse -->
              </div><!-- /.container-fluid -->
            </nav>

            <nav class="navbar navbar-default">
              <div class="container-fluid">
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav navbar-left">
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="{{asset('img/principal/mis_islas.png')}}">
                      </a>
                      <ul class="dropdown-menu">
                        <li><a href="#">Jovenes Empresarios</a></li>
                        <li><a href="#">Sitios turisticos</a></li>
                        <li><a href="#">Información turistica</a></li>
                      </ul>
                    </li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="{{asset('img/principal/asi_vamos.png')}}">
                      </a>
                      <ul class="dropdown-menu">
                        <li><a href="#">Reconstrucción</a></li>
                        <li><a href="#">Plan de desarrollo</a></li>
                        <li><a href="#">Proyectos municipales</a></li>
                        <li><a href="#">Avance fiscal</a></li>
                        <li><a href="#">Centro de desarrollo infantil</a></li>
                        <li><a href="#">Centro de vida</a></li>
                      </ul>
                    </li>
                  </ul>
                  <ul class="nav navbar-nav navbar-right redes-sociales">
                    <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>

                  </ul>
                </div><!-- /.navbar-collapse -->
              </div><!-- /.container-fluid -->
            </nav>
        </div>
        @yield('contenido')
        <div class="modal fade" id="modal-ingresar" tabindex="-2" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-body">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <form class="form_entrar col-md-12" action="{{url('/login')}}" method="POST">
                  {{ csrf_field() }}
                    <h4 class="text-center text-white">Ingreso a la Plataforma</h4>
                    <div class="form-group">
                      <input type="text" name="email" class="form-control input-lg" placeholder="Email">
                      
                      @if ($errors->has('email'))
                          <span class="help-block">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <input type="password" name="password" class="form-control input-lg" placeholder="Password">
                     @if ($errors->has('password'))
                          <span class="help-block">
                              <strong>{{ $errors->first('password') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="form-group">
                      <button class="btn btn-info btn-lg btn-block" type="submit">Entrar</button>
                    </div>
                  </form>
              </div>
              <div class="modal-footer">
                  &nbsp;
              </div>
            </div>
          </div>
        </div>
    </body>
    <!-- jQuery -->
    <script src="{{ asset('/js/jquery.js') }}"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="{{ asset('/assets/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- DataTables JavaScript -->

    <script src="{{asset('/assets/datatables/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/assets/datatables-plugins/dataTables.bootstrap.min.js')}}"></script>

    <!--data tables-->
    <script src="{{ asset('js/lib/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit') }}"></script>


    <!--toast-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- FLOT PIE PLUGIN - also used to draw donut charts -->
    {{-- <script src="{{ asset('adminLTE/plugins/jQuery/jQuery-2.2.0.min.js')}}"></script> --}}

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<html/>

