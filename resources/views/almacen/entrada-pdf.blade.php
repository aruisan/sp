
@extends('layouts.almacenPdf')
@section('contenido')
		<div class="row">
			<center><h3>Comprobante de Entrada de Almacen</h3></center>
		</div>
		<div style="border:1px solid black;">
			<div style="width: 70%;   display: inline-block; margin-left: 3%">
				<h4>Fecha: {{date('Y-m-d')}}</h4>
			</div>
			
			<div style="width: 20%;  display: inline-block; border:1px solid black; margin: 6px 0px 0px 0px;" class="col-md-2">
				<h4>Número {{ $ingreso->id }}</h4>
			</div> 
		</div>

        <div style="border:1px solid black;">
			<div style="width: 45%;   display: inline-block; margin-left: 3%">
				<h4>No. Factura: {{$ingreso->factura}}</h4>
			</div>
			
			<div style="width: 45%;  display: inline-block; margin: 6px 0px 0px 0px;" class="col-md-2">
				<h4>Fecha Factura: {{$ingreso->fecha_factura}}</h4>
			</div> 
		</div>

        <div style="border:1px solid black;">
			<div style="width: 45%;   display: inline-block; margin-left: 3%">
				<h4>Contrato: {{$ingreso->contrato}}</h4>
			</div>
			
			<div style="width: 45%;  display: inline-block; margin: 6px 0px 0px 0px;" class="col-md-2">
				<h4>Fecha del Contrato: {{$ingreso->fecha_contrato}}</h4>
			</div> 
		</div>

        <div style="border:1px solid black;">
			<div style="width: 90%;   display: inline-block; margin-left: 3%">
				<h4>Proovedor: {{$ingreso->proovedor->nombre}}</h4>
			</div>
		</div>
				
		<div class="br-black-1">
            <center>
                <h3>Articulos</h3>
            </center>
                <table class="table table-bordered" id="tabla_INV">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Nombre del Articulo</th>
                            <th class="text-center">Marca</th>
                            <th class="text-center">Presentación</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Valor Unitario</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">ccd</th>
                            <th class="text-center">ccc</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ingreso->articulos as $key => $item)
                            <tr class="text-center">
                                <td>{{ $key+1 }}</td>
                                <td>{{ $item->nombre_articulo}}</td>
                                <td>{{ $item->marca }}</td>
                                <td>{{ $item->presentacion }}</td>
                                <td>{{ $item->cantidad}}</td>
                                <td>{{ $item->valor_unitario}}</td>
                                <td>{{ $item->total}}</td>
                                <td>{{ $item->puc_ccd->code}}</td>
                                <td>{{ is_null($item->puc_ccd->almacen_puc_credito) ? "no tiene credito" : $item->puc_ccd->almacen_puc_credito->code}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
		</div>
		<div class="br-black-1">
            <center>
                <h3>Contabilidad</h3>
            </center>
            <table class="table table-bordered" id="tabla_INV">
                        <thead>
                            <tr>
                                <th class="text-center">cuenta contable debito</th>
                                <th class="text-center">cuenta contable debito Concepto</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">cuenta contable credito</th>
                                <th class="text-center">cuenta contable credito Concepto</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pucs as $puc)
                            <tr class="text-center">
                                <td>{{$puc->code}}</td>
                                <td>{{$puc->concepto}}</td>
                                <td>{{$puc->almacen_items->filter(function($item)use($ingreso){ return $item->almacen_comprobante_ingreso_id == $ingreso->id; })->sum('total')}}</td>
                                <td>{{$puc->almacen_puc_credito->code}}</td>
                                <td>{{$puc->almacen_puc_credito->concepto}}</td>
                                <td>{{$puc->almacen_items->filter(function($item)use($ingreso){ return $item->almacen_comprobante_ingreso_id == $ingreso->id; })->sum('total')}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
		</div>
@stop
