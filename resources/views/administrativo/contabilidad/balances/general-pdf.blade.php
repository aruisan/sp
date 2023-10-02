
@extends('layouts.balance_general_pdf')
@section('title')
    Balance General Periodo 1 de  {{$meses[$mes-1]}} al {{date("t", strtotime("2023-{$mes}-01"))}} del año {{date('Y')}}
@stop
@section('contenido')
        <table class="table">
            <tr>
                <td colspan="2">
                <center>
                    <h3>Balance General Periodo 1 de  {{$meses[$mes-1]}} al {{date("t", strtotime("2023-{$mes}-01"))}} del año {{date('Y')}}</h3>
                </center> 
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><b>{{$activo->puc_alcaldia->codigo_punto}}</b></td>
                                <td><b>{{$activo->puc_alcaldia->concepto}}</b></td>
                                <td></td>
                            </tr>
                            {!!$activo->format_hijos_general_pdf!!}
                        </tbody>
                    </table>
                </td>
                <td>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><b>{{$pasivo->puc_alcaldia->codigo_punto}}</b></td>
                                <td><b>{{$pasivo->puc_alcaldia->concepto}}</b></td>
                                <td></td>
                            </tr>
                            {!!$pasivo->format_hijos_general_pdf!!}

                            <tr>
                                <td><b>{{$patrimonio->puc_alcaldia->codigo_punto}}</b></td>
                                <td><b>{{$patrimonio->puc_alcaldia->concepto}}</b></td>
                                <td></td>
                            </tr>
                            {!!$patrimonio->format_hijos_general_pdf!!}
                            <tr>
                                <td>{{$puc_opcional->puc_alcaldia->codigo_punto}}</td>
                                <td>{{$puc_opcional->puc_alcaldia->concepto}}</td>
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
                                <td class="text-right"><b>${{number_format($activo->s_final ,0,",", ".")}}</b></td>
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
                                <td class="text-right"><b>${{number_format($patrimonio->s_final + $pasivo->s_final + $iguales_ingresos_gastos ,0,",", ".")}}</b></td>
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
