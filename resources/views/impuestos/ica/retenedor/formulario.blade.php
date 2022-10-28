@extends('layouts.ica_pdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive br-black-1">
			<table id="TABLA1" class="table text-center">
				<tbody>
				<tr style="background-color: #0e7224; color: white">
					<th scope="row" colspan="3">FORMULARIO UNICO NACIONAL DE DECLARACION Y PAGO DEL IMPUESTO DE INDUSTRIA Y COMERCIO</th>
				</tr>
				<tr style="background-color: #bfc3bf; color: black">
					<th scope="row" >MUNICIPIO O DISTRITO </th>
					<th scope="row" colspan="2">PROVIDENCIA Y SANTA CATALINA ISLAS</th>
				</tr>
				<tr style="background-color: #bfc3bf; color: black">
					<td>DEPARTAMENTO</td>
					<td colspan="2">ARCHIPIELAGO DE SAN ANDRES, PROVIDENCIA Y SANTA CATALINA</td>
				</tr>
				<tr style="background-color: #bfc3bf; color: black">
					<td>1. Año: {{ $formulario->añoGravable }}</td>
					<td>2. Periodo:
						@if($formulario->periodo == 1) Enero
						@elseif($formulario->periodo == 2) Febrero
						@elseif($formulario->periodo == 3) Marzo
						@elseif($formulario->periodo == 4) Abril
						@elseif($formulario->periodo == 5) Mayo
						@elseif($formulario->periodo == 6) Junio
						@elseif($formulario->periodo == 7) Julio
						@elseif($formulario->periodo == 8) Agosto
						@elseif($formulario->periodo == 9) Septiembre
						@elseif($formulario->periodo == 10) Octubre
						@elseif($formulario->periodo == 11) Noviembre
						@else Diciembre @endif
					</td>
					<td style="vertical-align: middle">
						@if($formulario->opciondeUso == "Declaración") DECLARACIÓN INICIAL
						@else CORRECCIÓN @endif
					</td>
				</tr>
				<tr style="background-color: #bfc3bf; color: black">
					<td colspan="3"><b>No. Formulario: {{$formulario->numReferencia}}</b></td>
				</tr>
				<tr style="background-color: #bfc3bf; color: black"><td colspan="3">Fecha de presentación <br>{{ Carbon\Carbon::parse($formulario->presentacion)->Format('d-m-Y')}}</td></tr>
				</tbody>
			</table>
			<table class="table text-center table-bordered">
				<tbody>
				<tr style="background-color: #0e7224; color: white"><th scope="row" colspan="3">Retenciones practicadas</th></tr>
				<tr>
					<td>12</td><td>Por contratos de obra o consultoría: </td>
					<td>$<?php echo number_format($formulario->contratosObra,0) ?></td>
				</tr>
				<tr>
					<td>13</td><td>Por Contratos de Prestación de servicios</td>
					<td>$<?php echo number_format($formulario->contratosPrestServ,0) ?></td>
				</tr>
				<tr>
					<td>14</td><td>Por Compras de bienes y servicios diferentes a los anteriors</td>
					<td>$<?php echo number_format($formulario->compraBienes,0) ?></td>
				</tr>
				<tr>
					<td>15</td><td>Por otras actividades gravadas</td>
					<td>$<?php echo number_format($formulario->otrasActiv,0) ?></td>
				</tr>
				<tr>
					<td>16</td><td>Practicadas en periodos anteriores dejadas de declarar</td>
					<td>$<?php echo number_format($formulario->practicadasPeriodosAnt,0) ?></td>
				</tr>
				<tr>
					<td>17</td><td>Total Retenciones practicadas</td>
					<td>$<?php echo number_format($formulario->totRetenciones,0) ?></td>
				</tr>
				</tbody>
			</table>
			<table class="table text-center table-bordered">
				<tbody>
				<tr style="background-color: #0e7224; color: white"><th scope="row" colspan="3">Pagos</th></tr>
				<tr>
					<td>18</td><td>Devolución por exceso de cobro</td>
					<td>$<?php echo number_format($formulario->devolucionExceso,0) ?></td>
				</tr>
				<tr>
					<td>19</td><td>Devolución retención practicada no aplicable</td>
					<td>$<?php echo number_format($formulario->devolucionRetencion,0) ?></td>
				</tr>
				<tr>
					<td>20</td><td>Total retención neta</td>
					<td>$<?php echo number_format($formulario->totalRetencion,0) ?></td>
				</tr>
				<tr>
					<td>21</td><td>Sanción por extemporaneidad o declarar</td>
					<td>$<?php echo number_format($formulario->sancionExtemp,0) ?></td>
				</tr>
				<tr>
					<td>22</td><td>Sanción por corrección o inexactitud</td>
					<td>$<?php echo number_format($formulario->sancionCorreccion,0) ?></td>
				</tr>
				<tr>
					<td>23</td><td>Intereses Moratorios</td>
					<td>$<?php echo number_format($formulario->interesMoratorio,0) ?></td>
				</tr>
				<tr>
					<td>24</td><td>Pago Total retenciones netas mas sanciones e intereses</td>
					<td>$<?php echo number_format($formulario->pagoTotal,0) ?></td>
				</tr>
				</tbody>
			</table>
			{{-- TABLA E. FIRMAS --}}
			<table id="TABLA7" class="table text-center table-bordered">
				<tbody>
				<tr><th style="background-color: #0e7224; color: white" scope="row" colspan="3">FIRMAS</th></tr>
				<tr>
					<td style="width: 300px">25. Identificación del signatario<br> {{ $formulario->idSignatario }}</td>
					<td colspan="2">26. Nombre del signatario <br> {{ $formulario->nameSignatario }}</td>
				</tr>
				<tr>
					<td style="vertical-align: middle" colspan="3">@if($formulario->signatario == "repLegal") Signatario Representante Legal
						@elseif($formulario->signatario == "delegado") Signatario Delegado o Con Poder
						@else Signatario Principal @endif</td>
				</tr>
				<tr>
					<td>30. T.P. Contador Revisor Fiscal signatario <br> {{ $formulario->tpRevFisc }}<br></td>
					<td colspan="2">31. Nombre del Contador o Revisor Fiscal <br> {{ $formulario->nameRevFisc }}<br></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
@stop
