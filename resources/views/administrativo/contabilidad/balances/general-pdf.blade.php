
@extends('layouts.balance_general_pdf')
@section('title')
    {{$titulo}}
@stop
@section('contenido')
        <table class="table">
            <tr>
                <td colspan="2">
                <center>
                    <h3>{{$titulo}}</h3>
                </center> 
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><b>{{$activo->first()->puc_alcaldia->codigo_punto}}</b></td>
                                <td><b>{{$activo->first()->puc_alcaldia->concepto}}</b></td>
                                <td></td>
                            </tr>
                            @foreach($activo_h->groupBy('puc_alcaldia_id') as $hijo)
                            <tr>
                                <td>{{$hijo->first()->puc_alcaldia->codigo_punto}}</td>
                                <td>{{$hijo->first()->puc_alcaldia->concepto}}</td>
                                <td>${{number_format($hijo->sum('s_final') ,0,",", ".")}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
                <td>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><b>{{$pasivo->first()->puc_alcaldia->codigo_punto}}</b></td>
                                <td><b>{{$pasivo->first()->puc_alcaldia->concepto}}</b></td>
                                <td></td>
                            </tr>
                            @foreach($pasivo_h->groupBy('puc_alcaldia_id') as $hijo)
                            <tr>
                                <td>{{$hijo->first()->puc_alcaldia->codigo_punto}}</td>
                                <td>{{$hijo->first()->puc_alcaldia->concepto}}</td>
                                <td>${{number_format($hijo->sum('s_final') ,0,",", ".")}}</td>
                            </tr>
                            @endforeach

                            <tr>
                                <td><b>{{$patrimonio->first()->puc_alcaldia->codigo_punto}}</b></td>
                                <td><b>{{$patrimonio->first()->puc_alcaldia->concepto}}</b></td>
                                <td></td>
                            </tr>
                            @foreach($patrimonio_h->groupBy('puc_alcaldia_id') as $hijo)
                            <tr>
                                <td>{{$hijo->first()->puc_alcaldia->codigo_punto}}</td>
                                <td>{{$hijo->first()->puc_alcaldia->concepto}}</td>
                                <td>${{number_format($hijo->sum('s_final') ,0,",", ".")}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td>{{$puc_opcional->first()->puc_alcaldia->codigo_punto}}</td>
                                <td>{{$puc_opcional->first()->puc_alcaldia->concepto}}</td>
                                <td class="text-right">${{number_format($iguales_ingresos_gastos ,0,",", ".")}}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><b>Sumas Iguales:</b></td>
                                <td></td>
                                <td class="text-right"><b>${{number_format($activo->sum('s_final') ,0,",", ".")}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="text-right"><b>${{number_format($patrimonio->sum('s_final') + $pasivo->sum('s_final') + $iguales_ingresos_gastos ,0,",", ".")}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
		
@stop

@section('firma')
        <div style="width:30%; display:inline-block;">
            _________________________<br>
            JORGE NORBERTO GARI HOKER	 <br>
            ALCALDE MUNICIPAL <br>
            
        </div>

        <div style="width:30%; display:inline-block;">
            _________________________<br>
            HELEN GARCIA ALEGRIA	 <br>
            CONTADORA <br>
        </div>

        <div style="width:30%; display:inline-block;">
            _________________________<br>
            JIM ANDERSON HENRY BENT	 <br>
            SECRETARIO DE HACIENDA <br>
        </div>
@endsection
