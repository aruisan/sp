@extends('layouts.frontend')

@section('css')
  <style type="text/css">
      .imagen-principal{
        background-image: url('{{asset('img/principal/Banner_974.jpg')}}');
         -webkit-background-size: cover;
         -moz-background-size: cover;
         -o-background-size: cover;
         background-size: cover;
         height: 100%;
         width: 90% ;
         margin-left: 5%;
         text-align: center;
         z-index: -3;
      }
  </style>
@stop
@section('contenido')

      <div>
        <img src="{{asset('img/principal/alcaldia_providencia.jpg')}}" alt="" class="img-responsive">
      </div>

      <!-- Modal -->
<div id="modal_celebracion" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <center>
        <img src="{{asset('celebracion/capacitacion_comerciantes.png')}}" class="img-responsive"/>
        </center>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>
@stop

@section('js')
      <script>
        $(document).ready(function(){
          {{--
            $('#modal_celebracion').modal();
            --}}
          setTimeout(function(){
            $('#modal_celebracion').modal('hide');
          }, 5000);
        });
      </script>
@endsection