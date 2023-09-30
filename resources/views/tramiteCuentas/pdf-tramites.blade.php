<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>pdfffff</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }


        .bg-gris{
            background-color:#a6a9ac;
        }

        .bg-agua_marina{
            background-color:#43c8d785;
        }

        .bg-morado{
            background-color:#da28f1ba;
        }

        .tf-10{
            font-size:10px;
        }

        .tf-8{
            font-size:9px;
        }

        .text-center{
            text-align: center;
        }

        table{
            width:100%;
        }
    </style>
</head>
<body>
    <table class="table">
        <tr class="bg-gris text-center">
            <td>
                <img src="{{asset('/img/escudoIslas.png')}}" width="50px"/>
            </td>
            <td colspan="3" >
                PROCESO GESTION: <br>
                CONTRACTUAL<br>
                A-GC-FT-007
            </td>
            <td colspan="6" >
                CERTIFICACIÓN PARA PAGO DEL SUPERVISIÓN O<br>
                INTERVENTORÍA
            </td>
            <td colspan="2" >
                PÁGINA
            </td>
            <td colspan="2" >
                1 DE 1
            </td>
        </tr>
        <tr class="bg-gris">
            <td colspan="14">
                1. IDENTIFICACIÓN
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                CONTRATO DE:
            </td>
            <td colspan="3">
                <input  type="checkbox" checked> PRESTACIÓN DE SERVICIOS
            </td>
            <td colspan="2">
                <input  type="checkbox" checked> SUMINISTRO
            </td>
            <td colspan="3">
                <input  type="checkbox" checked> INTERADMINISTRATIVO
            </td>
            <td>
                <input  type="checkbox" checked> OBRA
            </td>
            <td colspan="2">
                <input  type="checkbox" checked> OTRO
            </td>
            <td>
                1927
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                OBJETO DE CONTRATO:
            </td>
            <td colspan="12">
                Contrato de prestación de servicios para desarrollo, implementación y capacitación 
            </td>
        </tr>
        <tr  class="tf-10 text-center">
            <td colspan="2">
                PLAZO DE EJECUCIÓN
            </td>
            <td colspan="4" class="bg-agua_marina"> 
                FECHA DE SUSCRIPCIÓN DEL CONTRATO 
            </td>
            <td colspan="4" class="bg-agua_marina"> 
                FECHA DE INICIO 
            </td>
            <td colspan="4" class="bg-agua_marina"> 
                FECHA DE TERMINACIÓN 
            </td>
        </tr>
        <tr class="tf-10 text-center">
            <td colspan="2">
                DEL CONTRATO:
            </td>
            <td>1</td>
            <td colspan="2">NOVIEMBRE</td>
            <td>2022</td>
            <td>1</td>
            <td colspan="2">NOVIEMBRE</td>
            <td>2022</td>
            <td>30</td>
            <td colspan="2">DICIEMBRE</td>
            <td>2022</td>
        </tr>
        <tr class="tf-10">
            <td colspan="2">
                CONTRATISTA:
            </td>
            <td colspan="12">
                JOSE FRANCISCO UNIVERSIDAD
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                CEDULA O NIT:
            </td>
            <td colspan="12">
                9008156-9
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                CONTRATISTA  CESIONARIO:
            </td>
            <td colspan="12">
                N/A
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                CÉDULA DEL CESIONARIO:
            </td>
            <td colspan="12">
                N/A
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2" rowspan="2">
                RÉGIMEN DEL CONTRATISTA <br>
                O PROVEEDOR:
            </td>
            <td colspan="4" class="bg-agua_marina">
                <input  type="checkbox" checked> SIMPLIFICADO
            </td>
            <td colspan="4" class="bg-agua_marina">
                <input  type="checkbox" checked> COMÚN
            </td>
            <td colspan="4" class="bg-agua_marina">
                <input  type="checkbox" checked> OTRO:
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="4" class="bg-agua_marina">
                <input  type="checkbox" checked> AUTORRETENEDOR
            </td>
            <td colspan="4" class="bg-agua_marina">
                <input  type="checkbox" checked> GRAN CONTRIBUYENTE
            </td>
            <td colspan="4" class="bg-agua_marina">
                <input  type="checkbox" checked> N.A
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                CODIGO ACTIVIDAD ICA:
            </td>
            @foreach(range(1,12) as $i)
            <td></td>
            @endforeach
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                DIRECCIÓN CONTRATISTA O PROVEEDOR:
            </td>
            <td colspan="7">
                direccion direccion direccion direccion
            </td>
            <td colspan="2">
                TELEFONO:
            </td>
            <td colspan="3">
                3015377921
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                CORREO ELECTRONICO:
            </td>
            <td colspan="12">
                Nluismoyar@hotmail.com
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                DATOS CUENTA: 
            </td>
            <td colspan="1">
                No.
            </td>
            <td colspan="5">
               123456789
            </td>
            <td colspan="2">
                CLASE:
            </td>
            <td colspan="2">
                <input  type="checkbox" checked> AHORROS:
            </td>
            <td colspan="2">
                <input  type="checkbox" checked> CORRIENTE:
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                BANCARIA UNICA: 
            </td>
            <td colspan="3">
                ENTIDAD BANCARIA:
            </td>
            <td colspan="9">
                BANCO DE BOGOTA
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                VALOR TOTAL DEL CONTRATO:
            </td>
            <td colspan="3">
                $150000000
            </td>
            <td colspan="9">
                TRESCIENTOS MILLONES DE PESOS
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                DISPONIBILIDAD PRESUPUESTAL:
            </td>
            <td colspan="12">
                2370
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                REGISTRO PRESUPUESTAL:
            </td>
            <td colspan="12">
                4116
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                NOMBRE O INTERVENTOR:
            </td>
            <td colspan="7">
            GREGG AMBROSIO HUFFIGNTON MAY 
            </td>
            <td colspan="2">
                DEPENDENCIA:
            </td>
            <td colspan="3">
                SISTEMAS
            </td>
        </tr>
        <tr class="bg-gris">
            <td colspan="14">
                2. ADICIÓN AL CONTRATO
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                VALOR ADICIÓN AL CONTRATO:
            </td>
            <td>
            </td>
            <td colspan="3">
            </td>
            <td colspan="4">
                IVA INCLUIDO EN LA ADICIÓN:
            </td>
            <td colspan="4">
                N/A
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                DISPONIBILIDAD PRESUPUESTAL:
            </td>
            <td colspan="12">
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                REGISTRO PRESUPUESTAL:
            </td>
            <td colspan="12">
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                RUBRO PRESUPUESTAL:
            </td>
            <td colspan="12">
            </td>
        </tr>
        <tr class="bg-gris">
            <td colspan="14">
                3. DATOS PARA EL PAGO
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                NÚMERO DEL PAGO:
            </td>
            <td colspan="4">
                1
            </td>
            <td colspan="3">
                PERÍODO DEL PAGO:
            </td>
            <td colspan="5">
                
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                VALOR DEL PAGO:
            </td>
            <td colspan="4">
                $75000000
            </td>
            <td colspan="3">
                IVA INCLUIDO DEL PAGO:
            </td>
            <td colspan="5">
                1
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                AMORTIZACIÓN DEL PAGO:
            </td>
            <td colspan="12">
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                VALOR TOTAL ACTA:
            </td>
            <td colspan="12">
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                FACTURA O DOCUMENTO EQUIVALENTE:
            </td>
            <td colspan="6">
                FE -91-30/11/2022
            </td>
            <td colspan="3">
                DOCUMENTO EQUIVALENTE No.
            </td>
            <td colspan="3">
                N/A
            </td>
        </tr>
        <tr  class="tf-10 bg-morado">
            <td colspan="2">
                SE ACOGE A LA DISMINUCIÓN BASE GRAV.
            </td>
            <td colspan="3">
            <input  type="checkbox" checked> SI
            </td>
            <td colspan="3">
            <input  type="checkbox" checked> NO
            </td>
            <td colspan="6">
            <input  type="checkbox" checked> ANEXA CERTIFICACIÓN
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                VALOR DEL CONTRATO:
            </td>
            <td colspan="12">
                $150.000.000
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                VALOR CONTRATO EJECUTADO:
            </td>
            <td colspan="12">
                $75.000.000
            </td>
        </tr>
        <tr  class="tf-10">
            <td colspan="2">
                SALDO DEL CONTRATO:
            </td>
            <td colspan="12">
                $75.000.000
            </td>
        </tr>
        <tr class="tf-8">
            <td colspan="14">
            Certificó que el contratista en mención, cumplió a cabalidad con las obligaciones establecidas en el contrato suscrito con la Alcaldia del municipio,  
            para el presente periodo y que se  verificó que el contratista realizó los pagos de Aportes al Sistema de Seguridad  Social o Parafiscales a que esta obligado 
            si  hubo lugar a ello. En consecuencia se puede tramitar el pago correspondiente del contrato en mención.
            </td>
        </tr>
        <tr class="tf-8">
            <td colspan="14">
                Expedido en  el municipio , de Providencias Islas  el día {{date('d')}} del mes de {{date('m')}} del año {{date('Y')}}
            </td>
        </tr>
    </table>
</body>
</html>