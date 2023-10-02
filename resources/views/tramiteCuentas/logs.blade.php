<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        body { 
            margin: 4px;
            font-size: 10px;
         }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
        <center><h3>Tramite de cuenta Radicado No. {{$tc->id}}</h3></center>
    </div>
    <div style="border:1px solid black;">
        <div style="width: 78%;   display: inline-block; margin-left: 3%">
            <h4>Fecha: {{Carbon\Carbon::now()->format("Y-m-d")}}</h4>
            <h4>Hora: {{Carbon\Carbon::now()->format("H:i")}}</h4>
        </div>
        <br><br>
        <table class="table" border="1px">
            <tbody>
                <tr>
                    <td>Beneficiario:</td>
                    <td>{{$tc->beneficiario->nombre}}</td>
                </tr>
                <tr>
                    <td>Fecha de Recibido:</td>
                    <td>{{$tc->fecha_recibido}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table class="table" border="1px">
            <tbody>
               <tr>
                    <td></td>
                    <td>Tipo:</td>
                    <td>Número:</td>
                    <td>Valor:</td>
                </tr>
                <tr>
                    <td>Contrato</td>
                    <td>{{$tc->tipo_contrato}}</td>
                    <td>{{$tc->n_contrato}}</td>
                    <td>{{$tc->v_contrato}}</td>
                </tr>
                 <tr>
                    <td>Pago</td>
                    <td>{{$tc->tipo_pago}}</td>
                    <td>{{$tc->n_pago}}</td>
                    <td>{{$tc->v_pago}}</td>
                </tr>
            </tbody>
        </table>
        <br><br>
         <div class="table-responsive">
            <table class="table table-hover table-bordered" border="1px">  
                    <tbody>
                        <tr>   
                            <td class="text-center col-md-2">DOCUMENTOS</td>
                            <td class="text-center">APROBADO</td>
                            <td class="text-center">MOTIVO DE DEVOLUCION</td>
                            <td class="text-center">OBSERVACIONES</td>
                        </tr>
                        @foreach ($tc->chequeosCuenta as  $item )
                            <tr>
                                <td>{{$item->requisitoChequeo->nombre}}</td>
                                <td>{{!$item->validar_chequeo ?'No': 'Si'}}</td>
                                <td>{{$item->devolucion}}</td>
                                <td>{{$item->observacion}}</td>
                            </tr>
                         @endforeach         
                    </tbody>
                </table>
        </div>
    </div>
    </div>

</body>
</html>
