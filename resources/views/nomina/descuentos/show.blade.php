
@extends('layouts.dashboard')

@section('title', 'CobroCoactivo')

@section('titulo')
    Nomia del mes {{$nomina->mes}} del año {{date('Y')}}
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
    <li class="nav-item">
		<a class="nav-link" href="{{route('nomina.'.$nomina->tipo.'s.index')}}"> {{ucfirst($nomina->tipo)}}s</a>
	</li>
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina-descuentos.index', $nomina->tipo)}}">Nominas</a>
	</li>
</ul>
     
<div class="tab-content" style="background-color: white">
    <h3 class="text-center" id="fecha"></h3>
    <br><br>
	<div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Salario minimo del año </td>
                        <td rowspan="2">{{date('Y')}}</td>
                        <td class="bg-secondary">$1.160.000</td>
                    </tr>
                    <tr>
                        <td>Auxilio de Trasnporte</td>
                        <td class="bg-secondary">$140.000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered" id="table">
            </table>
        </div>
    </div>
</div>



@stop

@section('js')

    <script>
        const movimientos = {!!$movimientos!!};
        const meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
        const cop = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            })
        const movimientos_data = movimientos.map(e => 
                [
                    e.nombre, 
                    e.num_dc, 
                    e.cargo, 
                    e.descuentos[0], 
                    e.descuentos[1],
                    e.descuentos[2],
                    e.descuentos[3],
                    e.descuentos[4],
                    e.descuentos[5],
                    e.descuentos[6],
                    e.descuentos[7],
                    e.total_descuentos,
                ]
        );

        $(document).ready(function(){
            fecha_principal();
        });

        const fecha_principal = () => {
            $('#fecha').html(`NOMINA DEL 1 al ${diasEnUnMes({{date('m')}}, {{date('Y')}})} de ${meses[parseInt({{date('n')}})-1]} de {{date('Y')}}`);
        }

        const diasEnUnMes = (mes, año) =>  {
            return new Date(año, mes, 0).getDate();
        }

        
        
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
                data: movimientos_data,  
                columns: [
                    { title: 'Nombre' },
                    { title: 'Cedula' },
                    { title: 'Cargo' },
                    { title: 'Popular' },
                    { title: 'Bogota' },
                    { title: 'Agrario' },
                    { title: 'Cooserpark' },
                    { title: 'Davivienda' },
                    { title: 'Juzgado' },
                    { title: 'Coocasa' },
                    { title: 'Sindicato' },
                    { title: 'Descuentos' },
                ],
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
