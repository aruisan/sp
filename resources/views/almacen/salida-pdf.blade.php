
@extends('layouts.almacenPdf')
@section('contenido')
		<div class="row">
			<center><h3>Comprobante de Salida de Almacen No. {{$egreso->index}}</h3></center>
		</div>
		<div style="border:1px solid black;">
			<div style="width: 70%;   display: inline-block; margin-left: 3%">
				<h4>Fecha: {{$egreso->fecha}}</h4>
			</div>
			
			<div style="width: 20%;  display: inline-block; border:1px solid black; margin: 6px 0px 0px 0px;" class="col-md-2">
				<h4>Número {{ $egreso->index }}</h4>
			</div> 
		</div>

        <div style="border:1px solid black;">
			<div style="width: 45%;   display: inline-block; margin-left: 3%">
				<h4>Dependencia: {{$egreso->dependencia->name}}</h4>
			</div>
			
			<div style="width: 45%;  display: inline-block; margin: 6px 0px 0px 0px;" class="col-md-2">
				<h4>Responsable: {{$egreso->responsable->nombre}}</h4>
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
                        @php 
                            foreach($pucs as $puc):
                                $pucs_array_debito[$puc->code] = 0;
                                $pucs_array_credito[$puc->almacen_puc_credito->code] = 0;
                            endforeach;
                        @endphp
                        @foreach($egreso->salidas_pivot as $key => $item)
                            <tr class="text-center">
                                <td>{{ $key+1 }}</td>
                                <td>{{ $item->articulo->nombre_articulo}}</td>
                                <td>{{ $item->articulo->marca }}</td>
                                <td>{{ $item->articulo->presentacion }}</td>
                                <td>{{ $item->cantidad}}</td>
                                <td>${{number_format($item->articulo->valor_unitario, 0, ',', '.')}}</td>
                                <td>${{number_format($item->total, 0, ',', '.')}}</td>
                                <td>{{ $item->articulo->puc_ccd->code}}</td>
                                <td>{{ is_null($egreso->puc_credito) ? "No se ha asignado" : $egreso->puc_credito->code}}</td>
                            </tr>
                            @php
                                $pucs_array_debito[$item->articulo->puc_ccd->code] += $item->total;
                                $pucs_array_credito[$item->articulo->puc_ccd->almacen_puc_credito->code] += $item->total;

                                print_r($pucs_array_debito);
                                print_r($pucs_array_credito);
                            @endphp
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
                                <th class="text-center">cuenta contable</th>
                                <th class="text-center">Concepto</th>
                                <th class="text-center">CC</th>
                                <th class="text-center">Responsable</th>
                                <th class="text-center">Debito</th>
                                <th class="text-center">Credito</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pucs as $puc)
                            <tr>
                                <td>{{$puc->code}}</td>
                                <td>{{$puc->concepto}}</td>
                                <td>{{$egreso->responsable->num_dc}}</td>
                                <td>{{$egreso->responsable->nombre}}</td>
                                <td></td>
                                <td>${{number_format($pucs_array_debito[$puc->code], 0, ',', '.')}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>{{is_null($egreso->puc_credito) ? "No se ha asignado" : $egreso->puc_credito->code}}</td>
                            <td>{{is_null($egreso->puc_credito) ? "No se ha asignado" : $egreso->puc_credito->concepto}}</td>
                            <td>{{$egreso->responsable->num_dc}}</td>
                            <td>{{$egreso->responsable->nombre}}</td>
                            <td>${{number_format($egreso->salidas_pivot->sum('total'), 0, ',', '.')}}</td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
		</div>
@stop

@section('firma')
        <div style="width:45%; display:inline-block;">
            _________________________<br>
            {{$egreso->responsable->nombre}}<br>
            CC: {{$egreso->responsable->num_dc}} <br>
            responsable
        </div>
        <div style="width:45%; display:inline-block;">
            _________________________<br>
            GUSTAVO FIGUEREDO <br>
            ALMACENISTA GENERAL <br>
            .
        </div>
@endsection
