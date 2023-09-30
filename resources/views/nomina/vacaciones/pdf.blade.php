
@extends('layouts.NominaPdf')
@section('contenido')
		<div class="row">
			<center><h3> NOMINA DE  VACACIONES  MES {{strtoupper($nomina->mes)}} DEL {{date('Y')}}</h3></center>
		</div>
        
			<div class="br-black-1">
				<table class="table table-condensed" style="margin: 5px 10px;">
					<thead>
					<tr>
						<th class="text-center">TIPO</th>
                        <th class="text-center">DIAS</th>
						<th class="text-center">NOMBRE</th>
                        <th class="text-center">SALARIO</th>
                        <th class="text-center">VACACIONES</th>
                        <th class="text-center">PRI VAC</th>
                        <th class="text-center">IND</th>
                        <th class="text-center">TOTAL</th>
					</tr>
					</thead>
					<tbody>
					@foreach($movimientos as $k => $movimiento)
						<tr class="text-center">
							<td> {{$movimiento->ind_vac}}</td>
                            <td> {{$movimiento->ind_vac == 'vacaciones' ? $movimiento->dias_vacaciones : $movimiento->dias_vacaciones_laborados}}</td>
                            <td> {{$movimiento->empleado->nombre}}</td>
                            <td> {{$movimiento->sueldo}}</td>
                            <td> {{$movimiento->v_vacaciones}}</td>
                            <td> {{$movimiento->v_prima_vacaciones}}</td>
                            <td> {{$movimiento->v_ind}}</td>
                            <td> {{$movimiento->ind_vac == 'vacaciones' ? $movimiento->total_vacaciones : $movimiento->total_indemnizacion}}</td>
						</tr>
					@endforeach
					</tbody>
                    <tfoot class="text-center">
                        <th colspan="4" class="text-left">Totales</th>
                        <th>{{$nomina->empleados_nominas->sum('v_vacaciones')}}</th>
                        <th>{{$nomina->empleados_nominas->sum('v_prima_vacaciones')}}</th>
                        <th>{{$nomina->empleados_nominas->sum('v_ind')}}</th>
                        <th>{{$nomina->empleados_nominas->sum('total_vacaciones') + $nomina->empleados_nominas->sum('total_indemnizacion')}}</th>
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
                            LIZBETH VALENZUELA <br>
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
