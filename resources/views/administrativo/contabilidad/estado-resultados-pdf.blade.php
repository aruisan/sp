
@extends('layouts.balance_general_pdf')
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
                        <td><b>{{$ingresos->first()->puc_alcaldia->codigo_punto}}</b></td>
                        <td><b>{{$ingresos->first()->puc_alcaldia->concepto}}</b></td>
                        <td></td>
                    </tr>
                    @foreach($ingresos_h->groupBy('puc_alcaldia_id') as $hijo)
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
                        <td><b>{{$gastos->first()->puc_alcaldia->codigo_punto}}</b></td>
                        <td><b>{{$gastos->first()->puc_alcaldia->concepto}}</b></td>
                        <td></td>
                    </tr>
                    @foreach($gastos_h->groupBy('puc_alcaldia_id') as $hijo)
                    <tr>
                        <td>{{$hijo->first()->puc_alcaldia->codigo_punto}}</td>
                        <td>{{$hijo->first()->puc_alcaldia->concepto}}</td>
                        <td>${{number_format($hijo->sum('s_final') ,0,",", ".")}}</td>
                    </tr>
                    @endforeach
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
                        <td class="text-right"><b>${{number_format($ingresos->sum('s_final') ,0,",", ".")}}</b></td>
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
                        <td class="text-right"><b>${{number_format($gastos->sum('s_final'),0,",", ".")}}</b></td>
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
