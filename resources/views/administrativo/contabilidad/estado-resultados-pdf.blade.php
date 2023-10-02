
@extends('layouts.balance_general_pdf')
@section('contenido')
<table class="table">
    <tr>
        <td colspan="2">
        <center>
            <h3>Estado de Resultado Periodo 1 de  {{$meses[$mes-1]}} al {{date("t", strtotime("2023-{$mes}-01"))}} del año {{date('Y')}}</h3>
        </center> 
        </td>
    </tr>
    <tr>
        <td>
            <table class="table">
                <tbody>
                    <tr>
                        <td><b>{{$ingresos->puc_alcaldia->codigo_punto}}</b></td>
                        <td><b>{{$ingresos->puc_alcaldia->concepto}}</b></td>
                        <td></td>
                    </tr>
                    {!!$ingresos->format_hijos_general_pdf!!}
                </tbody>
            </table>
        </td>
        <td>
            <table class="table">
                <tbody>
                    <tr>
                        <td><b>{{$gastos->puc_alcaldia->codigo_punto}}</b></td>
                        <td><b>{{$gastos->puc_alcaldia->concepto}}</b></td>
                        <td></td>
                    </tr>
                    {!!$gastos->format_hijos_general_pdf!!}
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
                        <td class="text-right"><b>${{number_format($ingresos->s_final ,0,",", ".")}}</b></td>
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
                        <td class="text-right"><b>${{number_format($gastos->s_final,0,",", ".")}}</b></td>
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
