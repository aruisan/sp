@extends('layouts.ica_pdf')
@section('contenido')
	<div class="col-md-12 align-self-center">
		<div class="table-responsive">
			<table id="TABLA1" class="table text-center">
				<tbody>
				<tr style="background-color: #0e7224; color: white">
					<th scope="row" colspan="3">FORMULARIO UNICO NACIONAL DE DECLARACION Y PAGO DEL IMPUESTO DE INDUSTRIA Y COMERCIO</th>
				</tr>
				<tr style="background-color: #bfc3bf; color: black">
					<th scope="row" >MUNICIPIO O DISTRITO </th>
					<th scope="row">PROVIDENCIA Y SANTA CATALINA ISLAS</th>
					<th scope="row">AÑO GRAVABLE: {{$formulario->añoGravable}}</th>
				</tr>
				<tr style="background-color: #bfc3bf; color: black">
					<td>Fecha Presentacion: {{ Carbon\Carbon::parse($formulario->presentacion)->Format('d-m-Y')}}</td>
					<td colspan="2">ARCHIPIELAGO DE SAN ANDRES, PROVIDENCIA Y SANTA CATALINA</td>
				</tr>
				<tr style="background-color: #bfc3bf; color: black">
					<td colspan="3"><b>No. Formulario: {{$formulario->numReferencia}}</b></td>
				</tr>
				</tbody>
			</table>
			{{-- TABLA B. BASE GRAVABLE --}}
			<br>
			<table class="table text-center table-bordered table-condensed">
				<tbody>
				<tr style="background-color: #0e7224; color: white">
					<th scope="row" colspan="2">B. BASE GRAVABLE</th>
				</tr>
				<tr>
					<td>8. TOTAL INGRESOS ORDINARIOS Y EXTRAORDINARIOS DEL PERIODO EN TODO EL PAIS:</td>
					<td>$<?php echo number_format($formulario->totIngreOrd,0) ?></td>
				</tr>
				<tr>
					<td>9. MENOS INGRESOS FUERA DE ESTE MUNICIPIO O DISTRITO</td>
					<td>$<?php echo number_format($formulario->menosIngreFuera,0) ?></td>
				</tr>
				<tr>
					<td>10. TOTAL INGRESOS ORDINARIOS Y EXTRAORDINARIOS DE ESTE MUNICIPIO (REGLÓN 8 MENOS 9)</td>
					<td>$<?php echo number_format($formulario->totIngreOrdin,0) ?></td>
				</tr>
				<tr>
					<td>11. MENOS INGRESOS POR DEVOLUCIONES, REBAJAS, DESCUENTOS</td>
					<td>$<?php echo number_format($formulario->menosIngreDevol,0) ?></td>
				</tr>
				<tr>
					<td>12. MENOS INGRESOS POR EXPORTACIONES Y VENTAS DE ACTIVOS FIJOS</td>
					<td>$<?php echo number_format($formulario->menosIngreExport,0) ?></td>
				</tr>
				<tr>
					<td>13. MENOS INGRESOS POR OTRAS ACTIVIDADES EXCLUIDAS O NO SUJETAS Y OTROS INGRESOS NO GRAVADOS</td>
					<td>$<?php echo number_format($formulario->menosIngreOtrasActiv,0) ?></td>
				</tr>
				<tr>
					<td>14. MENOS INGRESOS POR ACTIVIDADES EXCENTES EN ESTE MUNICIPIO O DISTRITO (POR ACUERDO)</td>
					<td>$<?php echo number_format($formulario->menosIngreActivExcentes,0) ?></td>
				</tr>
				<tr>
					<td>15. TOTAL INGRESOS GRAVABLES (RENGLON 10 MENOS 11,12,13 Y 14)</td>
					<td>$<?php echo number_format($formulario->totIngreGravables,0) ?></td>
				</tr>
				</tbody>
			</table>
			{{-- TABLA C. DISCRIMINACIÓN DE INGRESOS GRAVADOS Y ACTIVIDADES DESARROLLADAS EN ESTE MUNICIPIO O DISTRITO --}}
			<br>
			<table id="TABLA3" class="table text-center table-condensed">
				<tbody>
				<tr style="background-color: #0e7224; color: white">
					<th scope="row" colspan="3">C. DISCRIMINACIÓN DE INGRESOS GRAVADOS Y ACTIVIDADES DESARROLLADAS EN ESTE MUNICIPIO O DISTRITO</th>
				</tr>
				<tr>
					<th>ACTIVIDADES GRAVADAS</th>
					<th>CODIGO CLASIFICACION MUNICIPAL</th>
					<th>INGRESOS GRAVADOS</th>
				</tr>
				<tr>
					<td>ACTIVIDAD 1 (PRINCIPAL)</td>
					<td>{{ $formulario->codClasiMuni }}</td>
					<td>$<?php echo number_format($formulario->totIngreGravables,0) ?></td>
				</tr>
				<tr>
					<td>ACTIVIDAD 2</td>
					<td>{{ $formulario->codClasiMuni2 }}</td>
					<td>$<?php echo number_format($formulario->ingreGravados2,0)?></td>
				</tr>
				<tr>
					<td>ACTIVIDAD 3</td>
					<td>{{ $formulario->codClasiMuni3 }}</td>
					<td>$<?php echo number_format($formulario->ingreGravados3,0)?></td>
				</tr>
				<tr>
					<td>ACTIVIDAD 4</td>
					<td>{{ $formulario->codClasiMuni4 }}</td>
					<td>$<?php echo number_format($formulario->ingreGravados4,0)?></td>
				</tr>
				<tr>
					<td>OTRAS ACTIVIDADES</td>
					<td>{{ $formulario->codClasiMuni5 }}</td>
					<td>$<?php echo number_format($formulario->ingreGravados5,0) ?></td>
				</tr>
				<tr>
					<td colspan="2">16. TOTAL INGRESOS GRAVADOS</td>
					<td>$<?php echo number_format($formulario->totIngreGravado,0) ?></td>
				</tr>
				<tr>
					<td colspan="2">18. GENERACIÓN DE ENERGIA CAPACIDAD INSTALADA</td>
					<td>$<?php echo number_format($formulario->genEnergiaCapacidad,0) ?></td>
				</tr>
				</tbody>
			</table>
			<table id="TABLA3" class="table text-center table-condensed">
				<tbody>
				<tr>
					<th>ACTIVIDADES GRAVADAS</th>
					<th>TARIFA</th>
					<th>IMPUESTO INDUSTRIA Y COMERCIO</th>
				</tr>
				<tr>
					<td>ACTIVIDAD 1 (PRINCIPAL)</td>
					<td><?php echo number_format($formulario->tarifa,0) ?>%</td>
					<td>$<?php echo number_format($formulario->impIndyCom,0) ?></td>
				</tr>
				<tr>
					<td>ACTIVIDAD 2</td>
					<td><?php echo number_format($formulario->tarifa2,0) ?>%</td>
					<td>$<?php echo number_format($formulario->impIndyCom2,0) ?></td>
				</tr>
				<tr>
					<td>ACTIVIDAD 3</td>
					<td><?php echo number_format($formulario->tarifa3,0) ?>%</td>
					<td>$<?php echo number_format($formulario->impIndyCom3,0) ?></td>
				</tr>
				<tr>
					<td>ACTIVIDAD 4</td>
					<td><?php echo number_format($formulario->tarifa4,0) ?>%</td>
					<td>$<?php echo number_format($formulario->impIndyCom4,0) ?></td>
				</tr>
				<tr>
					<td>17. TOTAL IMPUESTO</td>
					<td>$<?php echo number_format($formulario->totImpuesto,0) ?></td>
				</tr>
				<tr>
					<td>19. IMP LEY 56 DE 1981</td>
					<td>$<?php echo number_format($formulario->impLey56,0) ?></td>
				</tr>
				</tbody>
			</table>
			{{-- TABLA D. LIQUIDACIÓN IMPUESTO --}}
			<br>
			<table id="TABLA4" class="table text-center table-bordered table-condensed">
				<tbody>
				<tr style="background-color: #0e7224; color: white">
					<th scope="row" colspan="2">D. LIQUIDACIÓN IMPUESTO</th>
				</tr>
				</tbody>
				<tr>
					<td>20. TOTAL IMPUESTO DE INDUSTRIA Y COMERCIO (RENGLÓN 17 + 19)</td>
					<td>$<?php echo number_format($formulario->totImpIndyCom,0)?></td>
				</tr>
				<tr>
					<td>21. IMPUESTO DE AVISOS Y TABLEROS (15% DEL RENGLÓN 20)</td>
					<td>$<?php echo number_format($formulario->impAviyTableros,0)?></td>
				</tr>
				<tr>
					<td>22. PAGO POR UNIDADES COMERCIALES ADICIONALES DEL SECTOR FINANCIERO</td>
					<td>$<?php echo number_format($formulario->pagoUndComer,0)?></td>
				</tr>
				<tr>
					<td>23. SOBRETASA BOMBERIL (Ley 1575 de 2012) (Si la hay, liquidela según el acuerdo municipal o distrital)</td>
					<td>$<?php echo number_format($formulario->sobretasaBomberil,0)?></td>
				</tr>
				<tr>
					<td>24. SOBRETASA DE SEGURIDAD (LEY 1421 DE 2011) (Si la hay, liquidela según el acuerdo municipal o distrital)</td>
					<td>$<?php echo number_format($formulario->sobretasaSeguridad,0)?></td>
				</tr>
				<tr>
					<td><b>25. TOTAL IMPUESTO A CARGO (RENGLONES 20+21+22+23+24)</b></td>
					<td>$<?php echo number_format($formulario->totImpCargo,0)?></td>
				</tr>
				<tr>
					<td>26. MENOS VALOR DE EXENCIÓN O EXONERACIÓN SOBRE EL IMPUESTO Y NO SOBRE LOS INGRESOS</td>
					<td>$<?php echo number_format($formulario->menosValorExencion,0)?></td>
				</tr>
				<tr>
					<td>27. MENOS RETENCIONES que le practicaron a favor de este municipio o distrito en este periodo</td>
					<td>$<?php echo number_format($formulario->menosRetenciones,0)?></td>
				</tr>
				<tr>
					<td>28. MENOS AUTORRETENCIONES practicadas a favor de este municipio o distrito en este periodo</td>
					<td>$<?php echo number_format($formulario->menosAutorretenciones,0)?></td>
				</tr>
				<tr>
					<td>29. MENOS ANTICIPO LIQUIDADO EN EL AÑO ANTERIOR</td>
					<td>$<?php echo number_format($formulario->menosAnticipoLiquidado,0)?></td>
				</tr>
				<tr>
					<td>30. ANTICIPO DEL AÑO SIGUIENTE (Si existe, liquide porcentaje según Acuerdo Municipal o distrital)</td>
					<td>$<?php echo number_format($formulario->anticipoAñoSiguiente,0)?></td>
				</tr>
				<tr>
					<td>31. SANCION: {{ $formulario->SANCIONES }}</td>
					<td>$<?php echo number_format($formulario->sancionesVal,0)?></td>
				</tr>
				<tr>
					<td>32. MENOS SALDO A FAVOR DEL PERIODO ANTERIOR SIN SOLICITUD DE DEVOLUCIÓN O COMPENSACIÓN</td>
					<td>$<?php echo number_format($formulario->menosSaldoaFavorPredio,0)?></td>
				</tr>
				<tr>
					<td>33. TOTAL SALDO A CARGO (RENGLÓN 25-26-27-28-29+30+31-32)</td>
					<td>$<?php echo number_format($formulario->totSaldoaCargo,0)?></td>
				</tr>
				@if($formulario->totSaldoaFavor != 0)
					<tr>
						<td>34. TOTAL SALDO A FAVOR (RENGLÓN 25-26-27-28-29+30+31-32) SI EL RESULTADO ES MENOR A CERO</td>
						<td>$<?php echo number_format($formulario->totSaldoaFavor,0)?></td>
					</tr>
				@endif
			</table>
			{{-- TABLA D. PAGO --}}
			<br>
			<table id="TABLA5" class="table text-center table-condensed">
				<tbody>
				<tr style="background-color: #0e7224; color: white">
					<th scope="row" colspan="2">D. PAGO	</th>
				</tr>
				<tr>
					<td>35. VALOR A PAGAR</td>
					<td>$<?php echo number_format($formulario->valoraPagar,0)?></td>
				</tr>
				<tr>
					<td>36. DESCUENTO POR PRONTO PAGO (Si existe, liquidelo según el Acuerdo Municipial o distrital)</td>
					<td>$<?php echo number_format($formulario->valorDesc,0)?></td>
				</tr>
				<tr>
					<td>37. INTERESES DE MORA</td>
					<td>$<?php echo number_format($formulario->interesesMora,0)?></td>
				</tr>
				<tr>
					<td><b>38. TOTAL A PAGAR (RENGLÓN 35-36+37)</b></td>
					<td><b>$<?php echo number_format($formulario->totPagar,0)?></b></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
@stop
