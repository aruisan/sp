
@extends('layouts.balance_general_pdf')
@section('contenido')
        <table class="table">
            <tr>
                <td colspan="2">
                <center>
                    <h3>Balance General  {{date('Y-m-d')}}</h3>
                </center> 
                </td>
            </tr>
            <tr>
                <td>
                    <span>
                        <b>{{$activo->puc_alcaldia->codigo_punto}} - {{$activo->puc_alcaldia->concepto}}<b>
                    </span>
                    <br>
                    {!!$activo->format_hijos_general_pdf!!}
                </td>
                <td>
                    <span>
                        <b>{{$pasivo->puc_alcaldia->codigo_punto}} - {{$pasivo->puc_alcaldia->concepto}}<b>
                    </span>
                    <br>
                    {!!$pasivo->format_hijos_general_pdf!!}
                    <br><br>

                    <span>
                        <b>{{$patrimonio->puc_alcaldia->codigo_punto}} - {{$patrimonio->puc_alcaldia->concepto}}<b>
                    </span>
                    <br>
                    {!!$patrimonio->format_hijos_general_pdf!!}
                </td>
            </tr>
            <tr>
                <td>
                    Sumas Iguales: {{$activo->s_final}}
                </td>
                <td>
                    {{$patrimonio->s_final + $pasivo->s_final}}
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
