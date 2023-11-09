
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
        @if(TRUE)
        const movimientos_data = movimientos.map(e => 
                [
                    e.nombre, 
                    e.num_dc, 
                    e.cargo, 
                    e.sueldo_basico, 
                    e.dias_trabajados, 
                    e.basico, 
                    e.v_horas_extras, 
                    e.v_horas_extras_festivos, 
                    e.v_horas_extras_nocturnas,
                    e.v_recargos_nocturnos,
                    e.v_bonificacion_servicios,
                    e.v_prima_antiguedad,
                    e.v_vacaciones,
                    e.v_prima_vacaciones,
                    e.v_prima_navidad,
                    e.v_ind,
                    e.retroactivo,
                    e.total_devengado,
                    e.ibc,
                    e.eps,
                    e.salud.empleador,
                    e.salud.empleado,
                    e.fondo_pensiones,
                    e.pension.empleador, 
                    e.pension.empleado,
                    e.fsp, 
                    e.tarifa_retefuente,
                    e.retefuente,
                    e.descuentos[0], 
                    e.descuentos[1],
                    e.descuentos[2],
                    e.descuentos[3],
                    e.descuentos[4],
                    e.descuentos[5],
                    e.descuentos[6],
                    e.descuentos[7],
                    @if(in_array($nomina->id, [58,62]))
                    e.reintegro,
                    @endif
                    e.total_descuentos,
                    e.total_deduccion,
                    e.neto,
                    e.tipo_cuenta_bancaria,
                    e.banco_cuenta_bancaria,
                    e.numero_cuenta_bancaria,
                    'CAJASAI',
                    '4%',
                    e.v_caja,
                    'POSITIVA COMPAÑIA DE SEGUROS',
                    3,
                    '2,436%',
                    Math.ceil((e.ibc * 0.02436)/100)*100,
                    '5%',
                    e.v_sena,
                    '3%',
                    e.v_icbf,
                    '5%',
                    e.v_esap,
                    '1%',
                    e.v_men
                ]
        );
        @else
        const movimientos_data = movimientos.map(e => 
                [
                    e.nombre, 
                    e.num_dc,
                    e.ibc,
                    e.eps,
                    e.porc_salud,
                    e.salud.empleado,
                    "si ibc <= 1 salario 0.04, si ibc es mayop de 1 y menor o igual a 2 salarios 0.1 lo demas en 0.12"
                ]
        );
        @endif

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
                @if(TRUE)
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
                    { title: 'Bonificación Servicios' },
                    { title: 'Prima Antiguedad' },
                    { title: 'Vacaciones' },
                    { title: 'Prima Vacaciones' },
                    { title: 'Prima Navidad' },
                    { title: 'Indemnización' },
                    { title: 'Retroactivo' },
                    { title: 'Devengado' },
                    { title: 'Ibc' },
                    { title: 'Entidad' },//salud
                    { title: 'Patron' },
                    { title: 'Empleado' },
                    { title: 'Pension' },
                    { title: 'Patron' },
                    { title: 'Empleado' },
                    { title: 'Fsp' },
                    { title: '%Rt Fte' },
                    { title: 'Rt Fte' },
                    { title: 'Popular' },
                    { title: 'Bogota' },
                    { title: 'Agrario' },
                    { title: 'Cooserpark' },
                    { title: 'Davivienda' },
                    { title: 'Juzgado' },
                    { title: 'Coocasa' },
                    { title: 'Sindicato' },
                    @if(in_array($nomina->id, [58,62]))
                    { title: 'Reintegro' },
                    @endif
                    { title: 'Descuentos' },
                    { title: 'Dedución' },
                    { title: 'Neto Pagar' },
                    { title: 'Tipo de Cuenta' },
                    { title: 'Entidad Bancaria' },
                    { title: '# Cuenta' },
                    { title: 'Caja' },
                    { title: 'Tarifa' },
                    { title: 'Valor Caja' },
                    { title: 'Riezgos' },
                    { title: 'Tarifa' },
                    { title: 'Riesgo' },
                    { title: 'Valor Riesgo' },
                    { title: '%SENA' },
                    { title: 'SENA' },
                    { title: '%ICBF' },
                    { title: 'ICBF' },
                    { title: '%Esap' },
                    { title: 'Esap' },
                    { title: '%MEN' },
                    { title: 'MEN' },
                ],
                @else
                columns: [
                    { title: 'Nombre' },
                    { title: 'Cedula' },
                    { title: 'Sueldo' },
                    { title: 'Entidad' },
                    { title: '%' },
                    { title: 'SALUD' },
                    { title: 'Regla' },
                ],
                @endif
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
