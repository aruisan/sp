
@extends('layouts.dashboard')
@section('titulo')
Todos los tramites de cuentas 
@stop

@section('content')
<div class="breadcrumb text-center">
    <strong>
         <h3 class="text-capitalize">tramites de cuentas</h3>
    </strong>
</div>
   <div class="container-fluid">
    <div class="row">
        <div class="panel panel-default widget col-md-12">
            <div class="panel-body">
                  <br><br>
                    <a type="button" class="btn btn-primary" href="{{route('tramites-cuentas.create')}}">
                    <i class="glyphicon glyphicon-plus-sign"></i>   
                    </a>                                                                                                         
                <br><br>
                
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="recibidos">

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" align="100%" id="table_recibidos">
                                <thead>
                                    <tr>
                                      <th colspan="3" class="text-center">Tramite</th>
                                      <th colspan="3" class="text-center">Datos del Contrato</th>
                                      <th colspan="3" class="text-center">Datos del Pago</th>
                                      <th colspan="2" class="text-center">Opciones</th>
                                      <th colspan="8" class="text-center">Aprobaciones</th>
                                    </tr>
                                    <tr>
                                      <th class="text-center col-md-2">No. Radicado</th>
                                      <th class="text-center">Fecha de Recibido</th>
                                      <th class="text-center">Nombre de Beneficiario</th>
                                      <th class="text-center">Numero</th>
                                      <th class="text-center">Tipo</th>
                                      <th class="text-center">Valor</th>
                                      <th class="text-center">Numero</th>
                                      <th class="text-center">Tipo</th>
                                      <th class="text-center">Valor</th>
                                      <th class="text-center"><i class="fa fa-code"></i></th>
                                      <th class="text-center"><span class="fa fa-pencil-square-o"></span></th>
                                      <th class="text-center"><span class="fa fa-file-pdf-o"></span></th>
                                      <th class="text-center">Remitente</th>
                                      <th class="text-center">Radicador</th>
                                      <th class="text-center">Presupuesto</th>
                                      <th class="text-center">Revisor</th>
                                      <th class="text-center">Contador</th>
                                      <th class="text-center">Jefe</th>
                                      <th class="text-center">Egreso</th>
                                      <th class="text-center">Tesorero</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    @foreach ($tramiteCuentas as  $item )
                                        <tr>
                                            <td>{{$item->id}}</td>
                                            <td>{{$item->fecha_recibido}}</td>
                                            <td>{{$item->beneficiario->nombre}}</td>
                                            <td> 
                                              <a class="btn btn-sm btn-link" href="{{route('chequeo-cuenta.form',$item->id)}}" title="Chequear tramite de cuentas">
                                                {{$item->n_contrato}}
                                              </a>  
                                            </td>
                                            <td>{{$item->tipo_contrato != 'Otros' ? $item->tipo_contrato : $item->otro_tipo_contrato}}</td>
                                            <td>${{$item->v_contrato ? $item->v_contrato : '0.0'}}</td>
                                            <td>{{$item->n_pago ? $item->n_pago : 'No tiene'}}</td>
                                            <td>{{$item->tipo_pago}}</td>
                                            <td>${{$item->v_pago ? $item->v_pago : '0.0'}}</td>
                                            <td> 
                                              <a class="btn btn-sm btn-primary" href="{{route('tramites-cuentas.logs',$item->id)}}" title="Logs">
                                                <span class="fa fa-code" aria-hidden="true"></span>
                                              </a>  
                                            </td>
                                            <td> 
                                              <a class="btn btn-sm btn-primary" href="{{route('tramites-cuentas.edit',$item->id)}}" title="Editar tramite de cuentas">
                                                <span class="fa fa-pencil-square-o" aria-hidden="true"></span>
                                              </a>  
                                            </td>
                                            <td> 
                                              <a class="btn btn-sm btn-primary" target="_blank" href="{{route('tramites-cuentas.pdf',$item->id)}}" title="Pdf tramite de cuentas">
                                                <span class="fa fa-file-pdf-o" aria-hidden="true"></span>
                                              </a>  
                                            </td>
                                            <td>Finalizado</td>
                                            @if($item->aprobadoresCuenta->count() > 6)
                                                <td>{!!$item->aprobadoresCuenta[0]->estado_recibido!!}</td>
                                                <td>{!!$item->aprobadoresCuenta[1]->estado_recibido!!}</td>
                                            @else
                                                <td>{!!$item->aprobadoresCuenta[0]->estado_recibido!!}</td>
                                                <td>Sin Asignar</td>
                                            @endif
                                        </tr>
                                     @endforeach 
                                                     
                                </tbody>
                            </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="modal-devolver" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="titulo-devolver"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="form-devolver" method="post">
                  {{ csrf_field() }}
                  <input type="hidden" name="id" id="input-devolver">
                  <textarea rows="3" name="observacion" class="form-control"></textarea>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="enviarEstado()">Confirmar Devolución</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        {{--
        <div class="modal fade" id="modalNewMessage" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
              
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body modal-body-form-juridico">
                <div class="row">
                    <h3 class="text-center">Ingrese Cobro Coactivo Predial</h3>
                    
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="submitCreateProcessJuridico">Crear Proceso</button>
              </div>
            </div>
          </div>
        </div>
          --}}
    </div>

   </div>
@stop

@section('css')
   <style>
       .div-sombra{
           margin: 10px;
        -webkit-box-shadow: 4px 3px 5px 0px rgba(0,0,0,0.75);
        -moz-box-shadow: 4px 3px 5px 0px rgba(0,0,0,0.75);
        box-shadow: 4px 3px 5px 0px rgba(0,0,0,0.75);
       }

      .modal-body-form-juridico{
          padding: 10px;
      } 

      .btn-amarillo{
          background-color:#f7dd16;
      }
      .btn-verde{
          background-color:#1eb53a;
      }
      .btn-rojo{
          background-color:#d62828;
      }
   </style>
@stop

@section('js')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
    <script src="{{asset('assets/ckeditor/ckeditor.js')}}"></script>
 
    <script type="text/javascript" src="{{asset('js/relacionarParticipantes.js')}}"></script>

    <script>
    
        $(document).ready(function() {
            $('#table_recibidos').DataTable( {
              order: [[ 0, "desc" ]],
    language: {
              "lengthMenu": "Mostrar _MENU_ registros",
              "zeroRecords": "No se encontraron resultados",
              "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
              "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
              "infoFiltered": "(filtrado de un total de _MAX_ registros)",
              "sSearch": "Buscar:",
              "oPaginate": {
                  "sFirst": "Primero",
                  "sLast":"Último",
                  "sNext":"Siguiente",
                  "sPrevious": "Anterior"
               },
               "sProcessing":"Procesando...",
          },
      //para usar los botones
      "pageLength": 5,   
      responsive: "true",
      dom: 'Bfrtilp',       
      buttons:[ 
              {
              extend:    'copyHtml5',
              text:      '<i class="fa fa-clone"></i> ',
              titleAttr: 'Copiar',
              className: 'btn btn-primary'
          },
          {
              extend:    'excelHtml5',
              title:     'Programas',
              text:      '<i class="fa fa-file-excel-o"></i> ',
              titleAttr: 'Exportar a Excel',
              className: 'btn btn-primary',
              exportOptions: {
                columns: [0,1,2,3,4,5]
            },
          },
          {
              extend:    'pdfHtml5',
              title:     'Programas',
              text:      '<i class="fa fa-file-pdf-o"></i> ',
              titleAttr: 'Exportar a PDF',     
              message : 'SIEX',
              header :true,
              orientation : 'landscape',
              pageSize: 'LEGAL',
              className: 'btn btn-primary',
              exportOptions: {
                columns: [0,1,2,3,4,5]
            },
               },
          {
              extend:    'print',
              text:      '<i class="fa fa-print"></i> ',
              titleAttr: 'Imprimir',
              className: 'btn btn-primary'
          },
      ]              

         });

        } );


        function enviarRecibido(id){
          $(location).attr('href','/tramites-cuentas/'+id+'/recibido');
        }

        function devolver(id){
          $('#modal-devolver').modal();
          $("#form-devolver").attr('action', 'tramites-cuentas/estado-devolver');
          $('#titulo-devolver').text('Devolucion del tramite');
          $('#input-devolver').val(id);
        }

        function aplazar(id){
          $('#modal-devolver').modal();
          $("#form-devolver").attr('action', 'tramites-cuentas/estado-aplazar');
          $('#titulo-devolver').text('Aplazar el tramite');
          $('#input-devolver').val(id);
        }

        function enviarEstado(){
          $('#form-devolver').submit();
        }
   </script>
@stop
