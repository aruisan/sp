
@extends('layouts.NominaPdf')
@section('contenido')
		<div class="row">
			<center><h3> NOMINA MES DE {{$nomina->mes}} DEL {{date('Y')}}</h3></center>
		</div>
        
			<div class="br-black-1">
				<table class="table table-condensed" style="margin: 5px 10px;">
					<thead>
					<tr>
						<th class="text-center">ID</th>
						<th class="text-center">No. ID</th>
						<th class="text-center">NOMBRE</th>
                        <th class="text-center">CARGO</th>
                        <th class="text-center">CODIGO</th>
                        <th class="text-center">GRADO</th>
                        <th class="text-center">DIAS LAB.</th>
                        <th class="text-center">TOTAL SUELDO DEVENGADO</th>
                        <th class="text-center">OTROS DEVENGADOS</th>
                        <th class="text-center">TOTAL DEVENGADO</th>
                        <th class="text-center">COTIZACIÓN PENSIÓN</th>
                        <th class="text-center">COTIZACIÓN SALUD</th>
                        <th class="text-center">OTROS DESCUENTOS</th>
                        <th class="text-center">TOTAL DESCUENTOS</th>
                        <th class="text-center">TOTAL A PAGAR</th>
					</tr>
					</thead>
					<tbody>
                    @php
                        $suma_pensiones = 0;
                        $suma_salud = 0;
                        $suma_descuentos = 0;
                    @endphp
					@foreach($nomina->empleados_nominas as $k => $movimiento)
                         @php
                            $suma_pensiones = $suma_pensiones + $movimiento->v_pension['empleado'];
                            $suma_salud = $suma_salud + $movimiento->v_salud['empleado'];
                            $suma_descuentos = $suma_descuentos + $movimiento->descuentos->sum('valor');
                        @endphp
						<tr class="text-center">
							<td>{{$k+1}} </td>
							<td> {{$movimiento->empleado->num_dc}}</td>
                            <td> {{$movimiento->empleado->nombre}}</td>
                            <td> {{$movimiento->empleado->cargo}}</td>
                            <td> {{$movimiento->empleado->codigo_cargo}}</td>
                            <td> {{$movimiento->empleado->grado}}</td>
                            <td> {{$movimiento->dias_laborados}}</td>
                            <td> {{number_format($movimiento->v_dias_laborados, 0, ',', '.')}}</td>
                            <td> {{number_format($movimiento->devengados, 0, ',', '.')}}</td>
                            <td> {{number_format($movimiento->total_devengado, 0, ',', '.')}}</td>
                            <td> {{number_format($movimiento->v_pension['empleado'], 0, ',', '.')}}</td>
                            <td> {{number_format($movimiento->v_salud['empleado'], 0, ',', '.')}}</td>
                            <td> {{number_format($movimiento->descuentos->sum('valor'), 0, ',', '.')}}</td>
                            <td> {{number_format($movimiento->total_deduccion, 0, ',', '.')}}</td>
                            <td> {{number_format($movimiento->neto_pagar, 0, ',', '.')}}</td>
						</tr>
					@endforeach
					</tbody>
                    <tfoot>
                        <th colspan="7" class="text-rigth">Totales</th>
                        <th>${{number_format($nomina->empleados_nominas->sum('v_dias_laborados'), 0, ',', '.')}}</th>
                        <th>${{number_format(0, 0, ',', '.')}}</th>
                        <th>${{number_format($nomina->empleados_nominas->sum('total_devengado'), 0, ',', '.')}}</th>
                        <th>${{number_format($suma_pensiones, 0, ',', '.')}}</th>
                        <th>${{number_format($suma_salud, 0, ',', '.')}}</th>
                        <th>${{number_format($suma_descuentos, 0, ',', '.')}}</th>
                        <th>${{number_format($nomina->empleados_nominas->sum('total_deduccion'), 0, ',', '.')}}</th>
                        <th>${{number_format($nomina->empleados_nominas->sum('neto_pagar'), 0, ',', '.')}}</th>
                    </tfoot>
				</table>
			</div>

    <div class="row" style="margin-top:125px;">
        <table style="width:100%;">
            <tbody>
                <tr>
                    <td class="text-center" style="width:25%">
                        ______________________________<br>
                            JORGE NORBERTO GARI <br>
                            ALCALDE MUNICIPAL
                    </td>
                    <td class="text-center" style="width:25%">
                        ______________________________<br>
                            JULISSA ARCHBOLD BORDEN <br>
                            SECRETARIA GENERAL
                    </td>
                    <td class="text-center" style="width:25%">
                        ______________________________<br>
                            JIM ANDERSON HENRY <br>
                            SECRETARIA HACIENDA
                    </td>
                    <td class="text-center" style="width:25%">
                        ______________________________<br>
                            JIM ANDERSON HENRY BEN <br>
                            JEFE DE PRESUPUESTO
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
      <div class="row" style="margin-top:100px;">
        <table style="width:100%;">
            <tbody>
                 <tr>
                    <td class="text-center" style="width:30%">
                        ______________________________<br>
                            HELEN GARCIA <br>
                            CONTADORA
                    </td>
                    <td class="text-center" style="width:30%">
                        ______________________________<br>
                           JUSTINO BRITTON HENRY <br>
                            TESORERO
                    </td>
                    <td class="text-center" style="width:30%">
                        ______________________________<br>
                            ELEUTERIO ARCHBOLD <br>
                            TECNICO - ELABORADOR
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@stop
