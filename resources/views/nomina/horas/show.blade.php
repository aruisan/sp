
@extends('layouts.dashboard')

@section('title', 'CobroCoactivo')

@section('titulo')
    Personas
@stop

@section('sidebar')
	{{-- <li><a href="{{route('personas.create')}}" class="btn btn-success">Nuevo Tercero</a></li> --}}
@stop

@section('css')
	<style>
        .ocultar{
            display:none;
        }
    </style>
@stop

@section('content')


<ul class="nav nav-pills">
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina-vacaciones.index')}}">Nominas de vacaciones</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Nomina de Vacaciones</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
	<div id="lista" class="tab-pane active"> <div class="breadcrumb text-center">
		<strong>
			<h3>
                <b>Nueva Nomina de Vacaciones</b>
                <b>{{$nomina->mes}} - {{date('Y')}}</b>
            </h3>
		</strong>
	</div>
	<div class="container-fluid">
        <div class="col-md-12">
                <table class="table" id="table">
                    <thead>
                        <th>Empleado</th>
                        <th>Sueldo</th>
                        <th>H. Extras</th>
                        <th>Valor</th>
                        <th>H. Extras Festivos</th>
                        <th>Valor</th>
                        <th>H. Extras Nocturnas</th>
                        <th>Valor</th>
                        <th>Recargos Nocturnos</th>
                        <th>Valor</th>
                        <th>Total</th>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $k => $movimiento)
                            <tr>

                                <td>
                                    {{$movimiento->empleado->nombre}}
                                </td>
                                <td>
                                    {{$movimiento->sueldo}}
                                </td>
                                <td>
                                    {{$movimiento->horas_extras}}
                                </td>
                                <td>
                                    {{$movimiento->v_horas_extras}}
                                </td>
                                <td>
                                    {{$movimiento->horas_extras_festivos}}
                                </td>
                                <td>
                                    {{$movimiento->v_horas_extras_festivos}}
                                </td>
                                <td>
                                    {{$movimiento->horas_extras_nocturnas}}
                                </td>
                                <td>
                                    {{$movimiento->v_horas_extras_nocturnas}}
                                </td>
                                <td>
                                    {{$movimiento->recargos_nocturnos}}
                                </td>
                                <td>
                                    {{$movimiento->v_recargos_nocturnos}}
                                </td>
                                <td id="td_total_{{$k}}">
                                    {{$movimiento->v_horas_extras+$movimiento->v_horas_extras_festivos+$movimiento->v_horas_extras_nocturnas+$movimiento->v_recargos_nocturnos}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
	</div>
</div>



@stop

@section('js')
    <script>

$('#table').DataTable( {
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
                    "sProcessing":"Procesando..."
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
                        text:      '<i class="fa fa-file-excel-o"></i> ',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-primary'
                    },
                    {
                        extend:    'pdfHtml5',
                        text:      '<i class="fa fa-file-pdf-o"></i> ',
                        titleAttr: 'Exportar a PDF',     
                        message : 'SIEX',
                        header :true,
                        exportOptions: {
                            columns: [ 0,1,2,3,4]
                        },
                        pageSize: 'LEGAL',
                        className: 'btn btn-primary',
                    },
                    {
                        extend:    'print',
                        text:      '<i class="fa fa-print"></i> ',
                        titleAttr: 'Imprimir',
                        className: 'btn btn-primary'
                    }
                ]	             
            });
   </script>
@stop
