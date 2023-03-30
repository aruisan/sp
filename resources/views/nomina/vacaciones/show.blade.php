
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
                        <th>Tipo</th>
                        <th>Dias</th>
                        <th>Empleado</th>
                        <th>Sueldo</th>
                        <th>Vacaciones</th>
                        <th>Prima de Vacaciones</th>
                        <th>Indemnización</th>
                        <th>Total</th>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $k => $movimiento)
                            <tr>
                                <td>
                                    {{$movimiento->ind_vac}}
                                </td>
                                <td>
                                    {{$movimiento->ind_vac == 'vacaciones' ? $movimiento->dias_vacaciones : $movimiento->dias_vacaciones_laborados}}
                                </td>
                                <td>
                                    {{$movimiento->empleado->nombre}}
                                </td>
                                <td>
                                    {{$movimiento->sueldo}}
                                </td>
                                <td id="td_vac_{{$k}}">
                                    {{$movimiento->v_vacaciones}}
                                </td>
                                <td id="td_pv_{{$k}}">
                                    {{$movimiento->v_prima_vacaciones}}
                                </td>
                                <td id="td_ind_{{$k}}">
                                    {{$movimiento->v_ind}}
                                </td>
                                <td id="td_total_{{$k}}">
                                    {{$movimiento->total_vacaciones}}
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
