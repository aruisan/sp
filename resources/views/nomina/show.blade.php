
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
		<a class="nav-link" href="{{route('nomina.empleados.index')}}"> Empleados</a>
	</li>
	<li class="nav-item ">
		<a class="nav-link"  href="{{route('nomina.index', 'empleado')}}">Nominas</a>
	</li>
	<li class="nav-item active">
		<a class="nav-link">Crear Nomina</a>
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
                [e.nombre, e.num_dc, e.cargo, cop.format(e.sueldo_basico), 
                e.dias_trabajados, cop.format(e.basico), cop.format(e.v_horas_extras), cop.format(e.v_horas_extras_festivos), cop.format(e.v_horas_extras_nocturnas),cop.format(e.v_recargos_nocturnos),
                cop.format(e.v_bonificacion_direccion), cop.format(e.v_bonificacion_servicios), cop.format(e.v_bonificacion_recreacion), cop.format(e.v_prima_antiguedad),cop.format(e.retroactivo),
                cop.format(e.total_devengado), 
                
                cop.format(e.salud.empleado),cop.format(e.pension.empleado), cop.format(e.fsp), cop.format(e.retefuente),e.descuentos, cop.format(e.total_descuentos),cop.format(e.total_deduccion), 
                
                cop.format(e.neto),e.tipo_cuenta_bancaria,e.banco_cuenta_bancaria,e.numero_cuenta_bancaria  
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
                    { title: 'Sueldo Basico' },
                    { title: 'Dias Trabajados' },
                    { title: 'Basico' },
                    { title: 'H. Extras' },
                    { title: 'H. Extras Festivos' },
                    { title: 'H. Extras Nocturnas' },
                    { title: 'H. Recargo Nocturno' },
                    { title: 'Bonificación Dirección' },
                    { title: 'Bonificación Servicios' },
                    { title: 'Bonificación recreacion' },
                    { title: 'Prima Antiguedad' },
                    { title: 'Retroactivo' },
                    { title: 'Total Devengado' },
                    { title: 'Salud' },
                    { title: 'Pension' },
                    { title: 'Fsp' },
                    { title: 'Rt Fte' },
                    { title: 'Descuentos' },
                    { title: 'Total desceuntos' },
                    { title: 'Total Dedución' },
                    { title: 'Total Neto' },
                    { title: 'Tipo de Cuenta' },
                    { title: 'Entidad Bancaria' },
                    { title: '# Cuenta' }
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
