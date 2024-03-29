@extends('layouts.dashboard')
@section('titulo')
    Comprobantes de Contabilidad
@stop
@section('sidebar')
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Comprobantes de Contabilidad Vigencia {{ $vigencia->vigencia }}</b></h4>
        </strong>
    </div>

    <ul class="nav nav-pills">
        <li class="nav-item regresar">
            <a class="nav-link" href="{{ url('/presupuestoIng') }}" >Volver a Presupuesto de Ingresos</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" data-toggle="pill" href="#tabTareas">Comprobantes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/administrativo/CIngresos/create/'.$vigencia->id) }}" >NUEVO COMPROBANTE DE CONTABILIDAD</a>
        </li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active">
            <div class="table-responsive">
                <br>
                @if(count($CIngresos) > 0)
                    <table class="table table-bordered" id="tabla_Historico">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Tercero</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Ver</th>
                            <th class="text-center">PDF</th>
                            <th class="text-center"><i class="fa fa-trash"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($CIngresos as $historico)
                            <tr>
                                <td class="text-center">{{ $historico->code }}</td>
                                <td class="text-center">{{ $historico->ff }}</td>
                                <td class="text-center">{{ $historico->concepto }}</td>
                                <td class="text-center">{{ $historico->persona->num_dc }} - {{ $historico->persona->nombre }}</td>
                                <td class="text-center">$ <?php echo number_format($historico->valor,0) ?></td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/CIngresos/'.$historico->id.'/edit') }}" title="Ver Comprobante de Contabilidad" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/CIngresos/pdf/'.$historico->id) }}" target="_blank" title="Generar PDF" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                </td>
                                <td class="text-center">
                                    <a onclick="deleteCompCont({{$historico->id}})" title="Eliminar Comprobante Contable" class="btn-sm btn-primary"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <br><br>
                    <div class="alert alert-danger">
                        <center>
                            No hay Comprobantes de Contabilidad
                        </center>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
@section('js')

    <script type="text/javascript" >

        $(document).ready(function(){

            $('.nav-tabs a[href="#tabTareas"]').tab('show')
        });

    </script>

    <script>
        const vigenciaID = @json($vigencia->id);
        function deleteCompCont(id){
            var opcion = confirm("ESTA SEGURO DE ELIMIAR EL COMPROBANTE CONTABLE? AL ACEPTAR NO SE VAN A PODER REVERTIR LOS CAMBIOS REALIZADOS.");
            if (opcion == true) {

                $.ajax({
                    method: "DELETE",
                    url: "/administrativo/CIngresos/"+vigenciaID+"/"+id+"/delete",
                    data: { "_token": $("meta[name='csrf-token']").attr("content")}
                }).done(function(response) {
                    if (response == "OK"){
                        toastr.warning('COMPROBANTE CONTABLE ELIMINADO EXITOSAMENTE. RECARGANDO LA PAGINA....');
                        location. reload();
                    } else {
                        toastr.warning('SU USARIO NO TIENE PERMISOS PARA REALIZAR ESTA ACCIÓN.');
                    }

                }).fail(function() {
                    toastr.warning('OCURRIO UN ERROR AL ELIMINAR EL COMPROBANTE CONTABLE.');
                });
            }
        }

        $('#tabla_Historico').DataTable( {
        responsive: true,
        "searching": true,
        dom: 'Bfrtip',
        order: [[0, 'desc']],
        buttons:[
        {
            extend:    'copyHtml5',
            text:      '<i class="fa fa-clone"></i> ',
            titleAttr: 'Copiar',
            className: 'btn btn-primary'
        },
        {
            extend:    'excelHtml5',
            text:      '<i class="fa fa-file-excel-o"></i> ',
            titleAttr: 'Exportar a Excel',
            className: 'btn btn-primary'
        },
        {
            extend:    'pdfHtml5',
            text:      '<i class="fa fa-file-pdf-o"></i> ',
            titleAttr: 'Exportar a PDF',
            message : 'SIEX-Providencia',
            header :true,
            orientation : 'landscape',
            pageSize: 'LEGAL',
            className: 'btn btn-primary',
        },
        {
            extend:    'print',
            text:      '<i class="fa fa-print"></i> ',
            titleAttr: 'Imprimir',
            className: 'btn btn-primary'
        },
            ]
        } );
    </script>
@stop
